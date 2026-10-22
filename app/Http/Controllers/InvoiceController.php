<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SiteSetting;
use App\Services\InvoiceNumbering;
use App\Services\InvoicePdf;
use App\Services\InvoiceTotals;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('packageOrder')->orderBy('created_at', 'desc')->get();

        return view('dashboard.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $invoiceNumber = InvoiceNumbering::next();

        return view('dashboard.invoices.create', compact('invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required',
            'issue_date' => 'required|date',
            'client_phone' => 'nullable|string|max:30',
            'items' => 'required|array|min:1',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'client_address' => $request->client_address,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'Draft',
            'notes' => $request->notes,
            'discount' => $request->discount ?? 0,
            'discount_type' => $request->discount_type ?? 'percent',
            'tax' => $request->tax ?? 0,
            'tax_type' => $request->tax_type ?? 'percent',
            'total_amount' => 0,
        ]);

        $subtotal = 0;
        foreach (array_values($request->items) as $position => $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $itemSubtotal,
                'position' => $position,
            ]);
            $subtotal += $itemSubtotal;
        }

        $invoice->update([
            'total_amount' => InvoiceTotals::compute($subtotal, $invoice->discount, $invoice->discount_type, $invoice->tax, $invoice->tax_type)['total'],
        ]);

        return redirect()->route('dashboard.invoices.index')->with('success', 'Invoice created successfully');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load('items');

        return view('dashboard.invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'client_name' => 'required',
            'issue_date' => 'required|date',
            'client_phone' => 'nullable|string|max:30',
            'items' => 'required|array|min:1',
        ]);

        $invoice->update([
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'client_phone' => $request->client_phone,
            'client_address' => $request->client_address,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'status' => $request->status ?? 'Draft',
            'notes' => $request->notes,
            'discount' => $request->discount ?? 0,
            'discount_type' => $request->discount_type ?? 'percent',
            'tax' => $request->tax ?? 0,
            'tax_type' => $request->tax_type ?? 'percent',
        ]);

        // Refresh items
        $invoice->items()->delete();
        $subtotal = 0;
        foreach (array_values($request->items) as $position => $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $itemSubtotal,
                'position' => $position,
            ]);
            $subtotal += $itemSubtotal;
        }

        $invoice->update([
            'total_amount' => InvoiceTotals::compute($subtotal, $invoice->discount, $invoice->discount_type, $invoice->tax, $invoice->tax_type)['total'],
        ]);

        return redirect()->route('dashboard.invoices.index')->with('success', 'Invoice updated successfully');
    }

    public function show(Invoice $invoice)
    {
        $settings = SiteSetting::first();

        return view('dashboard.invoices.show', compact('invoice', 'settings'));
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully');
    }

    public function publicShow(Invoice $invoice)
    {
        $settings = SiteSetting::first();
        $invoice->load('items');
        
        return view('dashboard.invoices.show', compact('invoice', 'settings'))
            ->with('isPublic', true);
    }

    public function download(Invoice $invoice)
    {
        return InvoicePdf::make($invoice)->stream(InvoicePdf::filename($invoice));
    }
}
