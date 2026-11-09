<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    protected ?string $merchantCode;
    protected ?string $apiKey;
    protected bool $isSandbox;

    public function __construct()
    {
        $this->merchantCode = config('services.duitku.merchant_code');
        $this->apiKey = config('services.duitku.api_key');
        $this->isSandbox = (bool) config('services.duitku.sandbox', true);

        if (! $this->configured()) {
            Log::warning('Duitku configuration is missing. Please set DUITKU_MERCHANT_CODE and DUITKU_API_KEY in .env');
        }
    }

    public function configured(): bool
    {
        return filled($this->merchantCode) && filled($this->apiKey);
    }

    protected function endpoint(string $path): string
    {
        $host = $this->isSandbox ? 'https://sandbox.duitku.com' : 'https://passport.duitku.com';

        return $host.'/webapi/api/merchant/'.$path;
    }

    /**
     * Params: merchantOrderId, paymentAmount, productDetails, email, customerName,
     * optional: phoneNumber, paymentMethod, returnUrl, callbackUrl, expiryPeriod (minutes), itemDetails.
     */
    public function createInvoice($params)
    {
        if (! $this->configured()) {
            return [
                'success' => false,
                'message' => 'Konfigurasi Duitku belum lengkap. Silakan hubungi admin.',
            ];
        }

        $merchantOrderId = $params['merchantOrderId'];
        $paymentAmount = (int) $params['paymentAmount'];

        $signature = md5($this->merchantCode.$merchantOrderId.$paymentAmount.$this->apiKey);

        $payload = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => mb_substr($params['productDetails'], 0, 255),
            'email' => $params['email'],
            'phoneNumber' => $params['phoneNumber'] ?? '',
            'customerVaName' => mb_substr($params['customerName'], 0, 20),
            'callbackUrl' => $params['callbackUrl'] ?? route('payment.duitku.callback'),
            'returnUrl' => $params['returnUrl'] ?? route('checkout.success', $merchantOrderId),
            'expiryPeriod' => (int) ($params['expiryPeriod'] ?? 1440),
            'signature' => $signature,
        ];

        if (! empty($params['paymentMethod'])) {
            $payload['paymentMethod'] = $params['paymentMethod'];
        }

        if (! empty($params['itemDetails'])) {
            $payload['itemDetails'] = $params['itemDetails'];
        }

        try {
            $response = Http::post($this->endpoint('v2/inquiry'), $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['statusCode']) && $data['statusCode'] == '00') {
                    return [
                        'success' => true,
                        'paymentUrl' => $data['paymentUrl'],
                        'reference' => $data['reference'],
                    ];
                }

                Log::error('Duitku API Error: '.($data['statusMessage'] ?? 'Unknown error'), (array) $data);

                return [
                    'success' => false,
                    'message' => $data['statusMessage'] ?? 'Terjadi kesalahan pada payment gateway.',
                ];
            }

            Log::error('Duitku Connection Error: '.$response->body());

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke payment gateway.',
            ];
        } catch (\Exception $e) {
            Log::error('Duitku Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pembayaran.',
            ];
        }
    }

    public function getPaymentMethods($amount)
    {
        if (! $this->configured()) {
            return [];
        }

        $datetime = date('Y-m-d H:i:s');
        $signature = hash('sha256', $this->merchantCode.(int) $amount.$datetime.$this->apiKey);

        try {
            $response = Http::post($this->endpoint('paymentmethod/getpaymentmethod'), [
                'merchantcode' => $this->merchantCode,
                'amount' => (int) $amount,
                'datetime' => $datetime,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json()['paymentFee'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Duitku Get Methods Error: '.$e->getMessage());
        }

        return [];
    }

    public function validateCallback(Request $request): bool
    {
        if (! $this->configured()) {
            return false;
        }

        $calcSignature = md5($request->merchantCode.$request->amount.$request->merchantOrderId.$this->apiKey);

        if (hash_equals($calcSignature, (string) $request->signature)) {
            return true;
        }

        Log::warning('Duitku Callback Signature Mismatch', [
            'merchantOrderId' => $request->merchantOrderId,
        ]);

        return false;
    }

    /**
     * Asks Duitku for the transaction's current state.
     * Returns ['statusCode' => '00'|'01'|'02', 'amount' => int, 'reference' => ?string],
     * or null when Duitku could not be reached.
     */
    public function checkTransactionStatus(string $merchantOrderId): ?array
    {
        if (! $this->configured()) {
            return null;
        }

        try {
            $response = Http::post($this->endpoint('transactionStatus'), [
                'merchantCode' => $this->merchantCode,
                'merchantOrderId' => $merchantOrderId,
                'signature' => md5($this->merchantCode.$merchantOrderId.$this->apiKey),
            ]);

            if (! $response->successful()) {
                Log::warning('Duitku status check failed', ['merchantOrderId' => $merchantOrderId, 'http' => $response->status()]);

                return null;
            }

            $data = $response->json();

            return [
                'statusCode' => (string) ($data['statusCode'] ?? ''),
                'amount' => (int) ($data['amount'] ?? 0),
                'reference' => $data['reference'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::warning('Duitku status check error: '.$e->getMessage(), ['merchantOrderId' => $merchantOrderId]);

            return null;
        }
    }

    /**
     * A successful callback is only trusted when Duitku itself does not contradict it.
     * If the status API is unreachable we fall back on the (already verified) signature.
     */
    public function confirmsPaid(string $merchantOrderId, int $expectedAmount): bool
    {
        $status = $this->checkTransactionStatus($merchantOrderId);

        if ($status === null) {
            return true;
        }

        if ($status['statusCode'] !== '00') {
            Log::warning('Duitku status API does not confirm payment', ['merchantOrderId' => $merchantOrderId] + $status);

            return false;
        }

        if ($status['amount'] > 0 && $status['amount'] !== $expectedAmount) {
            Log::warning('Duitku status API amount mismatch', ['merchantOrderId' => $merchantOrderId, 'expected' => $expectedAmount] + $status);

            return false;
        }

        return true;
    }
}
