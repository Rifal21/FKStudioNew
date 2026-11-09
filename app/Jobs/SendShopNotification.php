<?php

namespace App\Jobs;

use App\Mail\ShopOrderMail;
use App\Models\ShopOrder;
use App\Models\SiteSetting;
use App\Services\Shop\Money;
use App\Services\Shop\ShopNotifier;
use App\Services\Waha\WahaClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendShopNotification implements ShouldQueue
{
    use Queueable;

    /** Events that may send WhatsApp to the customer's (unverified) checkout number. */
    private const CUSTOMER_WHATSAPP_EVENTS = [
        ShopNotifier::PAID,
        ShopNotifier::ACCESS_RESENT,
        ShopNotifier::SHIPPED,
        ShopNotifier::PROOF_REJECTED,
    ];

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public string $orderId,
        public string $event,
        public array $context = [],
    ) {
    }

    public function handle(): void
    {
        $order = ShopOrder::with('items')->find($this->orderId);

        if (! $order) {
            return;
        }

        $settings = SiteSetting::current();
        [$customer, $admin] = $this->messages($order, $settings);

        if ($customer) {
            $this->safely(fn () => Mail::to($order->customer_email)->send(new ShopOrderMail($order, $customer['subject'], $customer['lines'], $customer['action'] ?? null, $order->customer_name)));

            // The checkout phone number is not verified, so WhatsApp only goes out once something real
            // happened on the order (payment, shipping, an admin decision). A fresh, unpaid order only
            // gets an email; otherwise anyone could make the business number message arbitrary people.
            // Customer-chosen text (their name) is never put into the WhatsApp message.
            if (in_array($this->event, self::CUSTOMER_WHATSAPP_EVENTS, true)) {
                $this->whatsApp($settings, $order->customer_phone, $customer['subject']."\n\n".implode("\n", $customer['lines']).(isset($customer['action']) ? "\n\n".$customer['action']['url'] : ''));
            }
        }

        if ($admin) {
            $this->whatsApp($settings, $settings->shop_admin_phone, $admin."\n".route('dashboard.shop.orders.show', $order));
        }
    }

    /** @return array{0: ?array{subject: string, lines: list<string>, action?: array{label: string, url: string}}, 1: ?string} */
    private function messages(ShopOrder $order, SiteSetting $settings): array
    {
        $no = $order->order_number;
        $total = Money::format($order->total_amount);
        $orderUrl = route('shop.orders.show', $order);
        $deadline = $order->expires_at?->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y H:i').' WIB';

        return match ($this->event) {
            ShopNotifier::PLACED => [[
                'subject' => "Pesanan {$no} menunggu pembayaran",
                'lines' => [
                    'Terima kasih sudah berbelanja.',
                    "Total pembayaran: {$total}.",
                    "Selesaikan pembayaran sebelum {$deadline} agar pesanan tidak dibatalkan otomatis.",
                ],
                'action' => ['label' => 'Lihat cara bayar', 'url' => $orderUrl],
            ], null],
            ShopNotifier::PROOF_SUBMITTED => [null, "Bukti transfer baru untuk pesanan {$no} ({$total}) perlu diverifikasi."],
            ShopNotifier::PROOF_REJECTED => [[
                'subject' => "Bukti transfer pesanan {$no} belum bisa kami terima",
                'lines' => [
                    "Bukti transfer untuk pesanan {$no} belum bisa kami verifikasi.",
                    'Alasan: '.($this->context['reason'] ?? '-'),
                    "Silakan unggah ulang bukti yang benar sebelum {$deadline}.",
                ],
                'action' => ['label' => 'Unggah ulang bukti', 'url' => $orderUrl],
            ], null],
            ShopNotifier::PAID, ShopNotifier::ACCESS_RESENT => [[
                'subject' => $this->event === ShopNotifier::PAID ? "Pembayaran pesanan {$no} diterima" : "Akses produk pesanan {$no}",
                'lines' => array_values(array_filter([
                    $this->event === ShopNotifier::PAID ? "Pembayaran {$total} untuk pesanan {$no} sudah kami terima." : "Berikut akses produk digital dari pesanan {$no}.",
                    $order->hasDigitalItems() ? 'Produk digital Anda sudah bisa diunduh/diakses dari halaman Produk Digital Saya.' : null,
                    $order->hasPhysicalItems() ? 'Barang fisik sedang kami siapkan. Nomor resi akan kami kirim setelah paket dikirim.' : null,
                ])),
                'action' => $order->hasDigitalItems()
                    ? ['label' => 'Buka Produk Digital Saya', 'url' => route('user.downloads')]
                    : ['label' => 'Lihat pesanan', 'url' => $orderUrl],
            ], $this->event === ShopNotifier::PAID ? "Pesanan baru lunas: {$no} ({$total}, via {$order->payment_channel})." : null],
            ShopNotifier::SHIPPED => [[
                'subject' => "Pesanan {$no} sudah dikirim",
                'lines' => [
                    "Pesanan {$no} sudah kami kirim.",
                    'Kurir: '.strtoupper((string) $order->courier_code).' '.$order->courier_service,
                    'Nomor resi: '.$order->tracking_number,
                ],
                'action' => ['label' => 'Lihat pesanan', 'url' => $orderUrl],
            ], null],
            ShopNotifier::ATTENTION => [null, "Pesanan {$no} perlu tindakan: ".$order->attention_reason],
            default => [null, null],
        };
    }

    private function whatsApp(SiteSetting $settings, ?string $phone, string $text): void
    {
        if (! $settings->waha_enabled || blank($settings->waha_api_key) || WahaClient::normalizePhone($phone) === null) {
            return;
        }

        $this->safely(fn () => WahaClient::fromSettings($settings)->sendText($phone, $text));
    }

    private function safely(callable $send): void
    {
        try {
            $send();
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
