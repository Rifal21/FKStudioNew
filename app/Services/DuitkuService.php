<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    protected $merchantCode;
    protected $apiKey;
    protected $isSandbox;
    protected $baseUrl;

    public function __construct()
    {
        $this->merchantCode = env('DUITKU_MERCHANT_CODE');
        $this->apiKey = env('DUITKU_API_KEY');
        $this->isSandbox = env('DUITKU_IS_SANDBOX', true);
        $this->baseUrl = $this->isSandbox 
            ? 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' 
            : 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry';

        if (empty($this->merchantCode) || empty($this->apiKey)) {
            Log::warning('Duitku configuration is missing. Please set DUITKU_MERCHANT_CODE and DUITKU_API_KEY in .env');
        }
    }

    public function createInvoice($params)
    {
        if (empty($this->merchantCode) || empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'Konfigurasi Duitku belum lengkap. Silakan hubungi admin.',
            ];
        }

        $merchantOrderId = $params['merchantOrderId'];
        $paymentAmount = $params['paymentAmount'];
        
        $signature = md5($this->merchantCode . $merchantOrderId . $paymentAmount . $this->apiKey);

        $payload = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => (int) $paymentAmount,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => $params['productDetails'],
            'email' => $params['email'],
            'phoneNumber' => $params['phoneNumber'] ?? '',
            'customerVaName' => $params['customerName'],
            'callbackUrl' => route('payment.duitku.callback'),
            'returnUrl' => route('checkout.success', $merchantOrderId),
            'expiryPeriod' => 1440, // 24 hours
            'signature' => $signature,
        ];

        if (isset($params['paymentMethod'])) {
            $payload['paymentMethod'] = $params['paymentMethod'];
        }

        try {
            $response = Http::post($this->baseUrl, $payload);
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['statusCode']) && $data['statusCode'] == '00') {
                    return [
                        'success' => true,
                        'paymentUrl' => $data['paymentUrl'],
                        'reference' => $data['reference'],
                    ];
                }
                
                Log::error('Duitku API Error: ' . ($data['statusMessage'] ?? 'Unknown error'), $data);
                return [
                    'success' => false,
                    'message' => $data['statusMessage'] ?? 'Terjadi kesalahan pada payment gateway.',
                ];
            }

            Log::error('Duitku Connection Error: ' . $response->body());
            return [
                'success' => false,
                'message' => 'Gagal terhubung ke payment gateway.',
            ];
        } catch (\Exception $e) {
            Log::error('Duitku Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pembayaran.',
            ];
        }
    }

    public function getPaymentMethods($amount)
    {
        if (empty($this->merchantCode) || empty($this->apiKey)) {
            return [];
        }

        $datetime = date('Y-m-d H:i:s');
        $signature = hash('sha256', $this->merchantCode . $amount . $datetime . $this->apiKey);

        $url = $this->isSandbox 
            ? 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            : 'https://passport.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod';

        try {
            $response = Http::post($url, [
                'merchantcode' => $this->merchantCode,
                'amount' => (int) $amount,
                'datetime' => $datetime,
                'signature' => $signature,
            ]);

            if ($response->successful()) {
                return $response->json()['paymentFee'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Duitku Get Methods Error: ' . $e->getMessage());
        }

        return [];
    }

    public function validateCallback($request)
    {
        $merchantCode = $request->merchantCode;
        $amount = $request->amount;
        $merchantOrderId = $request->merchantOrderId;
        $signature = $request->signature;
        $apiKey = $this->apiKey;

        $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);

        if ($signature === $calcSignature) {
            return true;
        }

        Log::warning('Duitku Callback Signature Mismatch', [
            'received' => $signature,
            'calculated' => $calcSignature,
            'payload' => $request->all(),
        ]);

        return false;
    }
}
