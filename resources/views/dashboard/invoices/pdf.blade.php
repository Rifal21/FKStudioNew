<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            line-height: 1.4;
        }
        .container {
            padding: 25px;
            position: relative;
            min-height: 290mm;
        }
        .watermark {
            position: absolute;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(0,0,0,0.02);
            z-index: -1;
            text-transform: uppercase;
            white-space: nowrap;
            letter-spacing: 10px;
        }
        
        /* Header Styles */
        .header-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .logo-text {
            font-size: 28px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -1px;
        }
        .logo-text span {
            color: #2563eb;
        }
        .company-info {
            margin-top: 10px;
            max-width: 240px;
        }
        .company-name {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .company-address {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 500;
        }
        
        .thank-you {
            text-align: center;
            margin-top: 40px;
            color: #cbd5e1;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 5px;
            font-size: 9px;
        }
        .thank-you span {
            display: inline-block;
            width: 5px;
            height: 5px;
            background-color: #e2e8f0;
            border-radius: 50%;
            margin: 0 5px;
        }
        
        .invoice-badge {
            background-color: #2563eb;
            color: #ffffff;
            padding: 6px 16px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 10px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 15px;
        }
        .invoice-number {
            font-size: 24px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 5px;
            letter-spacing: -1px;
        }
        .invoice-date {
            font-size: 10px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .invoice-date span {
            color: #0f172a;
        }

        /* Details Grid */
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #f8fafc;
            padding-bottom: 15px;
        }
        .section-label {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #2563eb;
            margin-bottom: 12px;
        }
        .client-name {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }
        .client-email {
            font-size: 11px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 4px;
        }
        .client-address {
            font-size: 11px;
            color: #64748b;
            max-width: 320px;
        }
        
        .status-badge {
            padding: 6px 16px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
        }
        .status-paid {
            background-color: #ecfdf5;
            color: #059669;
            border: 1px solid #d1fae5;
        }
        .status-unpaid {
            background-color: #fffbeb;
            color: #d97706;
            border: 1px solid #fef3c7;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .items-table th {
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 0;
            color: #94a3b8;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: left;
            font-weight: 900;
        }
        .items-table td {
            padding: 10px 0;
            border-bottom: 1px solid #f8fafc;
        }
        .item-desc {
            font-size: 11px;
            font-weight: 900;
            color: #0f172a;
        }
        .item-qty {
            text-align: center;
            font-weight: 900;
            color: #475569;
        }
        .item-price {
            text-align: right;
            color: #64748b;
            font-weight: 500;
        }
        .item-amount {
            text-align: right;
            font-weight: 900;
            color: #0f172a;
        }

        /* Footer Layout */
        .footer-table {
            width: 100%;
        }
        .payment-methods {
            margin-bottom: 15px;
        }
        .bank-item {
            margin-bottom: 10px;
        }
        .bank-name {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
        }
        .bank-number {
            font-size: 12px;
            font-weight: 900;
            color: #2563eb;
        }
        .bank-holder {
            font-size: 8px;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: bold;
        }
        
        .qris-box {
            margin-top: 15px;
        }
        .qris-img {
            width: 85px;
            height: 85px;
            padding: 6px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
        }

        .totals-card {
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 15px;
            width: 280px;
            float: right;
        }
        .total-row {
            margin-bottom: 10px;
        }
        .total-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
        }
        .total-value {
            float: right;
            font-weight: 900;
            color: #0f172a;
            font-size: 11px;
        }
        .grand-total-row {
            border-top: 1px solid #e2e8f0;
            margin-top: 15px;
            padding-top: 15px;
        }
        .grand-total-label {
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #0f172a;
        }
        .grand-total-value {
            float: right;
            font-size: 20px;
            font-weight: 900;
            color: #2563eb;
            margin-top: -5px;
        }

        .signature-box {
            text-align: center;
            margin-top: 25px;
            float: right;
            width: 280px;
        }
        .signature-img {
            height: 60px;
            margin-bottom: 8px;
        }
        .signer-name {
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            color: #0f172a;
        }
        .signer-title {
            font-size: 8px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .thanks-footer {
            clear: both;
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #f8fafc;
        }
        .thanks-text {
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 5px;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="watermark">FKSTUDIO</div>

        <!-- Header Section -->
        <table class="header-table">
            <tr>
                <td style="vertical-align: top;">
                    @if($settings->invoice_logo)
                        <img src="{{ $settings->invoice_logo_url }}" style="height: 50px;">
                    @else
                        <div class="logo-text">FK<span>Studio</span></div>
                    @endif
                    <div class="company-info">
                        <div class="company-name">{{ $settings->invoice_company_name ?: 'FKStudio Agency' }}</div>
                        <div class="company-address">{{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}</div>
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="invoice-badge">Invoice</div>
                    <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                    <div class="invoice-date">Date <span>&bull;</span> {{ $invoice->issue_date->format('d F Y') }}</div>
                </td>
            </tr>
        </table>

        <!-- Details Section -->
        <table class="details-table">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <div class="section-label">Billed To</div>
                    <div class="client-name">{{ $invoice->client_name }}</div>
                    @if($invoice->client_email)
                        <div class="client-email">{{ $invoice->client_email }}</div>
                    @endif
                    <div class="client-address">{!! nl2br(e($invoice->client_address)) !!}</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div class="section-label" style="color: #94a3b8; margin-bottom: 8px;">Invoice Status</div>
                    <div class="status-badge {{ $invoice->status == 'Paid' ? 'status-paid' : 'status-unpaid' }}">
                        {{ $invoice->status }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Description</th>
                    <th style="text-align: center; width: 10%;">Qty</th>
                    <th style="text-align: right; width: 20%;">Price</th>
                    <th style="text-align: right; width: 20%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td><div class="item-desc">{{ $item->description }}</div></td>
                        <td class="item-qty">{{ $item->quantity }}</td>
                        <td class="item-price">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="item-amount">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer Section -->
        <table class="footer-table">
            <tr>
                <!-- Payment Methods -->
                <td style="width: 30%; vertical-align: top;">
                    <div class="section-label">Payment Methods</div>
                    <div class="payment-methods">
                        @if($settings->payment_methods)
                            @foreach($settings->payment_methods as $payment)
                                <div class="bank-item">
                                    <div class="bank-name">{{ $payment['bank'] }}</div>
                                    <div class="bank-number" style="font-size: 11px;">{{ $payment['number'] }}</div>
                                    <div class="bank-holder">{{ $payment['name'] }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </td>

                <!-- QRIS Section -->
                <td style="width: 35%; vertical-align: top; text-align: center;">
                    @if($settings->invoice_qris)
                        <div class="qris-box" style="display: inline-block; margin-top: 5px;">
                            <img src="{{ $settings->invoice_qris_url }}" class="qris-img" style="width: 100px; height: 100px; border-radius: 15px;">
                            <div style="font-size: 7px; font-weight: 900; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1px; margin-top: 5px;">Scan for faster payment</div>
                        </div>
                    @endif
                </td>

                <!-- Totals Section -->
                <td style="width: 35%; vertical-align: top;">
                    <div class="totals-card" style="float: none; width: 100%; margin-bottom: 20px; padding: 15px;">
                        @php 
                            $subtotal = $invoice->items->sum('subtotal');
                            $discountAmount = ($invoice->discount_type === 'percent') 
                                ? ($subtotal * ($invoice->discount / 100)) 
                                : $invoice->discount;
                            $taxAmount = ($invoice->tax_type === 'percent')
                                ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
                                : $invoice->tax;
                        @endphp
                        
                        <div class="total-row">
                            <span class="total-label">Subtotal</span>
                            <span class="total-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            <div style="clear: both;"></div>
                        </div>
                        
                        @if($invoice->discount > 0)
                            <div class="total-row">
                                <span class="total-label" style="color: #2563eb;">Discount</span>
                                <span class="total-value" style="color: #2563eb;">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                <div style="clear: both;"></div>
                            </div>
                        @endif

                        @if($invoice->tax > 0)
                            <div class="total-row">
                                <span class="total-label" style="color: #059669;">Tax</span>
                                <span class="total-value" style="color: #059669;">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</span>
                                <div style="clear: both;"></div>
                            </div>
                        @endif

                        <div class="total-row grand-total-row">
                            <span class="grand-total-label">Total</span>
                            <span class="grand-total-value" style="font-size: 22px;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                            <div style="clear: both;"></div>
                        </div>
                    </div>

                    <div class="signature-box" style="float: none; width: 100%; margin-top: 10px;">
                        @if($settings->invoice_signature)
                            <img src="{{ $settings->invoice_signature_url }}" class="signature-img" style="height: 45px;">
                        @else
                            <div style="height: 45px; border-bottom: 1px dashed #e2e8f0; width: 140px; margin: 0 auto 10px auto;"></div>
                        @endif
                        <div class="signer-name" style="font-size: 11px;">{{ $settings->invoice_signer_name ?: 'Rifal Kurniawan' }}</div>
                        <div class="signer-title" style="font-size: 7px;">{{ $settings->invoice_signer_title ?: 'Authorized Representative' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Notes Section -->
        <table style="width: 100%; margin-top: -40px;">
            <tr>
                <td style="width: 55%;">
                    @if($invoice->notes)
                        <div style="background-color: #f8fafc; padding: 12px; border-radius: 15px; border: 1px solid #f1f5f9;">
                            <div style="font-size: 8px; font-weight: 900; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 5px;">
                                <span style="display: inline-block; width: 10px; height: 10px; background: #e2e8f0; border-radius: 3px; margin-right: 5px; vertical-align: middle;"></span>
                                Notes / Terms
                            </div>
                            <div style="color: #64748b; font-size: 8px; line-height: 1.5;">{!! nl2br(e($invoice->notes)) !!}</div>
                        </div>
                    @endif
                </td>
                <td></td>
            </tr>
        </table>
            </tr>
        </table>

        <div class="thanks-footer">
            <div class="thanks-text">Thank you for your business</div>
            <div style="margin-top: 10px;">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</body>
</html>
