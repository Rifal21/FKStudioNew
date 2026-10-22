@php
    $subtotal = $invoice->items->sum('subtotal');
    $discountAmount = ($invoice->discount_type === 'percent')
        ? ($subtotal * ($invoice->discount / 100))
        : $invoice->discount;
    $taxAmount = ($invoice->tax_type === 'percent')
        ? (($subtotal - $discountAmount) * ($invoice->tax / 100))
        : $invoice->tax;
    $statusMap = [
        'Paid' => ['label' => 'Lunas', 'class' => 'status-paid'],
        'Sent' => ['label' => 'Terkirim', 'class' => 'status-sent'],
        'Unpaid' => ['label' => 'Belum dibayar', 'class' => 'status-unpaid'],
        'Draft' => ['label' => 'Draft', 'class' => 'status-unpaid'],
        'Cancelled' => ['label' => 'Dibatalkan', 'class' => 'status-cancelled'],
    ];
    $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'class' => 'status-unpaid'];
@endphp
<!DOCTYPE html>
<html lang="id">
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
            color: #0A1F3A;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            line-height: 1.45;
        }
        .top-bar {
            height: 6px;
            background-color: #0F7FCB;
        }
        .container {
            padding: 28px 32px;
        }

        /* Header */
        .header-table {
            width: 100%;
            margin-bottom: 18px;
        }
        .logo-text {
            font-size: 26px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .logo-text span {
            color: #0F7FCB;
        }
        .company-info {
            margin-top: 10px;
            max-width: 240px;
        }
        .company-name {
            font-size: 11px;
            font-weight: bold;
            color: #0A1F3A;
            margin-bottom: 2px;
        }
        .company-address {
            font-size: 10px;
            color: #465E78;
        }
        .invoice-badge {
            background-color: #0F7FCB;
            color: #ffffff;
            padding: 5px 16px;
            font-weight: bold;
            font-size: 11px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 10px;
        }
        .invoice-number {
            font-size: 20px;
            font-weight: bold;
            color: #0A1F3A;
            margin-bottom: 4px;
        }
        .invoice-date {
            font-size: 10px;
            color: #465E78;
        }
        .invoice-date b {
            color: #0A1F3A;
        }

        /* Details */
        .details-table {
            width: 100%;
            margin-bottom: 20px;
            border-top: 1px solid #CDE2F2;
            border-bottom: 1px solid #CDE2F2;
        }
        .details-table td {
            padding-top: 14px;
            padding-bottom: 14px;
        }
        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #0B66A6;
            margin-bottom: 6px;
        }
        .client-name {
            font-size: 16px;
            font-weight: bold;
            color: #0A1F3A;
            margin-bottom: 3px;
        }
        .client-email {
            font-size: 11px;
            font-weight: bold;
            color: #0B66A6;
            margin-bottom: 3px;
        }
        .client-address {
            font-size: 11px;
            color: #465E78;
        }
        .status-badge {
            padding: 5px 14px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
        }
        .status-paid {
            background-color: #E6FAF7;
            color: #0F766E;
            border: 1px solid #C6F3EC;
        }
        .status-sent {
            background-color: #E6F9FD;
            color: #0E7490;
            border: 1px solid #C2F0FA;
        }
        .status-unpaid {
            background-color: #FFF8E1;
            color: #855600;
            border: 1px solid #FFEFB8;
        }
        .status-cancelled {
            background-color: #FFF0F0;
            color: #B42318;
            border: 1px solid #FFDADA;
        }

        /* Items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }
        .items-table th {
            background-color: #EEF8FF;
            border-bottom: 1px solid #CDE2F2;
            padding: 8px 8px;
            color: #0B66A6;
            font-size: 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td {
            padding: 9px 8px;
            border-bottom: 1px solid #CDE2F2;
        }
        .item-desc {
            font-size: 11px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .item-qty {
            text-align: center;
            color: #0A1F3A;
        }
        .item-price {
            text-align: right;
            color: #465E78;
        }
        .item-amount {
            text-align: right;
            font-weight: bold;
            color: #0A1F3A;
        }

        /* Footer layout */
        .footer-table {
            width: 100%;
        }
        .bank-item {
            margin-bottom: 8px;
        }
        .bank-name {
            font-size: 10px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .bank-number {
            font-size: 12px;
            font-weight: bold;
            color: #0B66A6;
        }
        .bank-holder {
            font-size: 9px;
            color: #465E78;
        }
        .qris-img {
            width: 100px;
            height: 100px;
            padding: 5px;
            background: #ffffff;
            border: 1px solid #CDE2F2;
        }
        .qris-caption {
            font-size: 8px;
            color: #465E78;
            margin-top: 4px;
        }
        .notes-box {
            background-color: #EEF8FF;
            padding: 10px 12px;
            border: 1px solid #CDE2F2;
            border-radius: 8px;
            margin-top: 12px;
        }
        .notes-title {
            font-size: 9px;
            font-weight: bold;
            color: #465E78;
            margin-bottom: 4px;
        }
        .notes-text {
            font-size: 9px;
            color: #0A1F3A;
            line-height: 1.5;
        }

        .totals-card {
            background-color: #EEF8FF;
            padding: 12px 14px;
            border-radius: 8px;
        }
        .total-label {
            font-size: 10px;
            color: #465E78;
        }
        .total-value {
            text-align: right;
            font-weight: bold;
            color: #0A1F3A;
            font-size: 11px;
        }
        .grand-total-row td {
            border-top: 1px solid #CDE2F2;
            padding-top: 10px;
        }
        .grand-total-label {
            font-size: 11px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .grand-total-value {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #0B66A6;
        }

        .signature-box {
            text-align: center;
            margin-top: 22px;
        }
        .signature-img {
            height: 45px;
            margin-bottom: 6px;
        }
        .signature-line {
            height: 45px;
            border-bottom: 1px dashed #CDE2F2;
            width: 140px;
            margin: 0 auto 8px auto;
        }
        .signer-name {
            font-size: 11px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .signer-title {
            font-size: 9px;
            color: #465E78;
        }

        .thanks-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #CDE2F2;
        }
        .thanks-text {
            font-size: 10px;
            color: #465E78;
        }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <div class="container">

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
                    <div class="invoice-date">Tanggal: <b>{{ $invoice->issue_date->locale('id')->translatedFormat('d F Y') }}</b></div>
                </td>
            </tr>
        </table>

        <!-- Details Section -->
        <table class="details-table">
            <tr>
                <td style="width: 65%; vertical-align: top;">
                    <div class="section-label">Ditagihkan kepada</div>
                    <div class="client-name">{{ $invoice->client_name }}</div>
                    @if($invoice->client_email)
                        <div class="client-email">{{ $invoice->client_email }}</div>
                    @endif
                    <div class="client-address">{!! nl2br(e($invoice->client_address)) !!}</div>
                </td>
                <td style="text-align: right; vertical-align: bottom;">
                    <div class="section-label" style="color: #465E78; margin-bottom: 6px;">Status invoice</div>
                    <div class="status-badge {{ $st['class'] }}">{{ $st['label'] }}</div>
                </td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Deskripsi</th>
                    <th style="text-align: center; width: 10%;">Jml</th>
                    <th style="text-align: right; width: 20%;">Harga</th>
                    <th style="text-align: right; width: 20%;">Jumlah</th>
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
                <td style="width: 58%; vertical-align: top; padding-right: 18px;">
                    @if($invoice->status !== 'Paid')
                        <table style="width: 100%;">
                            <tr>
                                <td style="vertical-align: top;">
                                    <div class="section-label">Metode pembayaran</div>
                                    @if($settings->payment_methods)
                                        @foreach($settings->payment_methods as $payment)
                                            <div class="bank-item">
                                                <div class="bank-name">{{ $payment['bank'] }}</div>
                                                <div class="bank-number">{{ $payment['number'] }}</div>
                                                <div class="bank-holder">a.n. {{ $payment['name'] }}</div>
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                                @if($settings->invoice_qris)
                                    <td style="vertical-align: top; text-align: center; width: 115px;">
                                        <img src="{{ $settings->invoice_qris_url }}" class="qris-img">
                                        <div class="qris-caption">Pindai untuk bayar</div>
                                    </td>
                                @endif
                            </tr>
                        </table>
                    @endif

                    @if($invoice->notes)
                        <div class="notes-box">
                            <div class="notes-title">Catatan / ketentuan</div>
                            <div class="notes-text">{!! nl2br(e($invoice->notes)) !!}</div>
                        </div>
                    @endif
                </td>

                <!-- Totals Section -->
                <td style="width: 42%; vertical-align: top;">
                    <div class="totals-card">
                        <table style="width: 100%;">
                            <tr>
                                <td class="total-label" style="padding-bottom: 6px;">Subtotal</td>
                                <td class="total-value" style="padding-bottom: 6px;">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @if($invoice->discount > 0)
                                <tr>
                                    <td class="total-label" style="padding-bottom: 6px;">Diskon</td>
                                    <td class="total-value" style="padding-bottom: 6px;">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if($invoice->tax > 0)
                                <tr>
                                    <td class="total-label" style="padding-bottom: 6px;">Pajak</td>
                                    <td class="total-value" style="padding-bottom: 6px;">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="grand-total-row">
                                <td class="grand-total-label">Total</td>
                                <td class="grand-total-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="signature-box">
                        @if($settings->invoice_signature)
                            <img src="{{ $settings->invoice_signature_url }}" class="signature-img">
                        @else
                            <div class="signature-line"></div>
                        @endif
                        <div class="signer-name">{{ $settings->invoice_signer_name ?: 'Rifal Kurniawan' }}</div>
                        <div class="signer-title">{{ $settings->invoice_signer_title ?: 'Authorized Representative' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="thanks-footer">
            <div class="thanks-text">Terima kasih atas kepercayaan Anda.</div>
        </div>
    </div>
</body>
</html>
