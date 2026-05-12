<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        $lastInvoice = Invoice::where('invoice_number', 'LIKE', 'INV-%')
            ->latest()
            ->first();

        if ($lastInvoice) {
            $parts = explode('-', $lastInvoice->invoice_number);
            $lastNumber = (int) end($parts);
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        $invoiceNumber = "INV-$year$month$day-FKS-$nextNumber";

        return view('dashboard.invoices.create', compact('invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_name' => 'required',
            'issue_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        $invoice = Invoice::create([
            'invoice_number' => $request->invoice_number,
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
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
        foreach ($request->items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $itemSubtotal,
            ]);
            $subtotal += $itemSubtotal;
        }

        $discountAmount = ($invoice->discount_type === 'percent')
            ? ($subtotal * ($invoice->discount / 100))
            : $invoice->discount;

        $taxAmount = ($invoice->tax_type === 'percent')
            ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
            : $invoice->tax;

        $finalTotal = $subtotal - $discountAmount + $taxAmount;

        $invoice->update(['total_amount' => $finalTotal]);

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
            'items' => 'required|array|min:1',
        ]);

        $invoice->update([
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
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
        foreach ($request->items as $item) {
            $itemSubtotal = $item['quantity'] * $item['unit_price'];
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $itemSubtotal,
            ]);
            $subtotal += $itemSubtotal;
        }

        $discountAmount = ($invoice->discount_type === 'percent')
            ? ($subtotal * ($invoice->discount / 100))
            : $invoice->discount;

        $taxAmount = ($invoice->tax_type === 'percent')
            ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
            : $invoice->tax;

        $finalTotal = $subtotal - $discountAmount + $taxAmount;

        $invoice->update(['total_amount' => $finalTotal]);

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
        $settings = SiteSetting::first();
        $invoice->load('items');

        $pdf = Pdf::loadView('dashboard.invoices.pdf', compact('invoice', 'settings'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

        return $pdf->stream($invoice->invoice_number.'-'.$invoice->client_name.'.pdf');
    }
}
