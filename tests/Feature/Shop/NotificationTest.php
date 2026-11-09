<?php

namespace Tests\Feature\Shop;

use App\Models\ShopOrder;
use App\Services\Shop\OrderLifecycle;
use App\Services\Shop\ShopNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use BuildsShop, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duitkuConfig();
        $this->shopSettings(['waha_enabled' => true, 'waha_api_key' => 'k', 'waha_url' => 'http://waha.test', 'waha_session' => 'default']);
        Http::fake([
            'waha.test/*' => Http::response(['numberExists' => true, 'chatId' => '628111@c.us', 'id' => 'msg']),
            '*' => Http::response(['statusCode' => '00', 'paymentFee' => []]),
        ]);
    }

    public function test_unpaid_orders_never_trigger_whatsapp_to_the_checkout_number(): void
    {
        $user = $this->customer();
        $user->update(['name' => "Promo https://evil.example\nKlik sekarang"]);

        $order = $this->placeOrder($user, $this->digitalProduct(), ShopOrder::CHANNEL_MANUAL, input: ['phone' => '08111222333']);
        app(ShopNotifier::class)->orderPlaced($order);

        Http::assertNotSent(fn (Request $request) => str_contains($request->url(), 'waha.test'));
    }

    public function test_paid_orders_message_the_customer_without_their_free_text_name(): void
    {
        $user = $this->customer();
        $user->update(['name' => 'Promo https://evil.example']);
        $order = $this->placeOrder($user, $this->digitalProduct(), ShopOrder::CHANNEL_MANUAL, input: ['phone' => '08111222333']);

        app(OrderLifecycle::class)->markPaid($order, $order->payments()->first());

        Http::assertSent(fn (Request $request) => str_contains($request->url(), '/api/sendText')
            && str_contains($request['text'], $order->order_number)
            && ! str_contains($request['text'], 'evil.example'));
    }
}
