<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Services\InvoiceTotals;
use App\Services\InvoiceWhatsApp;
use App\Services\SubscriptionInvoiceGenerator;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with('items')->withCount('invoices')
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'paused' THEN 1 ELSE 2 END")
            ->orderBy('next_run_date')
            ->get();

        // Invoices made by a subscription that never reached the client.
        $pending = Invoice::with('subscription')
            ->whereNotNull('subscription_id')
            ->whereNull('wa_sent_at')
            ->where('status', 'Draft')
            ->latest('created_at')
            ->get();

        return view('dashboard.subscriptions.index', compact('subscriptions', 'pending'));
    }

    public function create()
    {
        return view('dashboard.subscriptions.form', ['subscription' => new Subscription([
            'billing_day' => 5,
            'due_days' => 7,
            'start_date' => SubscriptionInvoiceGenerator::today(),
            'discount_type' => 'percent',
            'tax_type' => 'percent',
            'auto_send' => false,
        ])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $subscription = Subscription::create($data['subscription'] + ['status' => Subscription::ACTIVE]);
        $this->syncItems($subscription, $data['items']);
        $subscription->scheduleNext(SubscriptionInvoiceGenerator::today());

        return redirect()->route('dashboard.subscriptions.show', $subscription)->with('success', 'Langganan dibuat. Invoice pertama terbit '.$subscription->fresh()->next_run_date?->locale('id')->translatedFormat('d F Y').'.');
    }

    public function show(Subscription $subscription)
    {
        $subscription->load('items', 'invoices');

        return view('dashboard.subscriptions.show', [
            'subscription' => $subscription,
            'totals' => $this->totals($subscription),
            'whatsapp' => new InvoiceWhatsApp(),
        ]);
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load('items');

        return view('dashboard.subscriptions.form', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $this->validated($request);

        $subscription->update($data['subscription']);
        $this->syncItems($subscription, $data['items']);

        if ($subscription->status !== Subscription::PAUSED) {
            $subscription->scheduleNext(SubscriptionInvoiceGenerator::today());
        }

        return redirect()->route('dashboard.subscriptions.show', $subscription)->with('success', 'Langganan diperbarui. Perubahan berlaku untuk invoice berikutnya.');
    }

    public function destroy(Subscription $subscription)
    {
        // Invoices already issued stay (subscription_id becomes null).
        $subscription->delete();

        return redirect()->route('dashboard.subscriptions.index')->with('success', 'Langganan dihapus. Invoice yang sudah terbit tetap ada.');
    }

    public function pause(Subscription $subscription)
    {
        $subscription->update(['status' => Subscription::PAUSED]);

        return back()->with('success', 'Langganan dijeda. Tidak ada invoice baru sampai dilanjutkan.');
    }

    public function resume(Subscription $subscription)
    {
        $subscription->update(['status' => Subscription::ACTIVE]);
        // Months skipped while paused are not billed retroactively.
        $subscription->scheduleNext(SubscriptionInvoiceGenerator::today());

        return back()->with('success', 'Langganan dilanjutkan. Invoice berikutnya '.$subscription->fresh()->next_run_date?->locale('id')->translatedFormat('d F Y').'.');
    }

    /** Bill the upcoming period right now (e.g. a client who wants the invoice early). */
    public function generateNow(Subscription $subscription, SubscriptionInvoiceGenerator $generator, InvoiceWhatsApp $whatsApp)
    {
        abort_if($subscription->status !== Subscription::ACTIVE, 422, 'Langganan tidak aktif.');

        $invoice = $generator->generate($subscription->load('items'));

        if ($invoice === null) {
            return back()->with('error', 'Invoice untuk periode itu sudah ada.');
        }

        $message = 'Invoice '.$invoice->invoice_number.' dibuat.';

        if ($subscription->auto_send) {
            $message .= $whatsApp->send($invoice)
                ? ' Terkirim lewat WhatsApp.'
                : ' Gagal dikirim: '.$invoice->fresh()->wa_error;
        }

        return redirect()->route('dashboard.subscriptions.show', $subscription)->with('success', $message);
    }

    private function totals(Subscription $subscription): array
    {
        return InvoiceTotals::compute(
            $subscription->subtotal(),
            (float) $subscription->discount,
            $subscription->discount_type,
            (float) $subscription->tax,
            $subscription->tax_type,
        );
    }

    private function validated(Request $request): array
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'client_phone' => 'nullable|string|max:30',
            'client_email' => 'nullable|email|max:255',
            'client_address' => 'nullable|string|max:1000',
            'billing_day' => 'required|integer|min:1|max:31',
            'due_days' => 'required|integer|min:0|max:90',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'discount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:percent,fixed',
            'tax' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:percent,fixed',
            'notes' => 'nullable|string|max:2000',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return [
            'subscription' => [
                ...$request->only(['title', 'client_name', 'client_phone', 'client_email', 'client_address', 'billing_day', 'due_days', 'start_date', 'end_date', 'notes']),
                'discount' => $request->input('discount') ?? 0,
                'discount_type' => $request->input('discount_type', 'percent'),
                'tax' => $request->input('tax') ?? 0,
                'tax_type' => $request->input('tax_type', 'percent'),
                'auto_send' => $request->boolean('auto_send'),
            ],
            'items' => array_values($request->input('items')),
        ];
    }

    private function syncItems(Subscription $subscription, array $items): void
    {
        $subscription->items()->delete();

        foreach ($items as $position => $item) {
            $subscription->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'position' => $position,
            ]);
        }
    }
}
