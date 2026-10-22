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
        'Draft' => ['label' => 'Draft', 'class' => 'status-draft'],
        'Cancelled' => ['label' => 'Dibatalkan', 'class' => 'status-cancelled'],
    ];
    $st = $statusMap[$invoice->status] ?? ['label' => $invoice->status, 'class' => 'status-unpaid'];
    $companyName = $settings->invoice_company_name ?: 'FKStudio IT Solution';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        /* dompdf: no flexbox/grid/shadows — layout is tables + borders. Palette = Sunny Sky. */
        @page { margin: 0; size: a4 portrait; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #0A1F3A;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            line-height: 1.45;
        }

        /* Masthead */
        .masthead { background-color: #FFCF0F; padding: 44px 40px 38px 40px; }
        .masthead-rule { height: 5px; background-color: #0A1F3A; }
        .logo-text { font-size: 28px; font-weight: bold; color: #0A1F3A; }
        .logo-text span { color: #0B66A6; }
        .company-name { margin-top: 12px; font-size: 11px; font-weight: bold; color: #0A1F3A; }
        .company-address { font-size: 10px; color: #3F3500; max-width: 250px; }
        .doc-title { font-size: 46px; font-weight: bold; color: #0A1F3A; line-height: 1; }
        .doc-number { margin-top: 8px; font-size: 13px; font-weight: bold; color: #0A1F3A; }

        .container { padding: 34px 40px 70px 40px; }

        /* Client + facts */
        .meta-table { width: 100%; margin-bottom: 34px; }
        .client-panel {
            background-color: #EEF8FF;
            border-left: 5px solid #0A1F3A;
            padding: 14px 18px;
        }
        .label { font-size: 9px; font-weight: bold; color: #0B66A6; margin-bottom: 4px; }
        .client-name { font-size: 17px; font-weight: bold; color: #0A1F3A; margin-bottom: 3px; }
        .client-email { font-size: 11px; font-weight: bold; color: #0B66A6; margin-bottom: 3px; }
        .client-address { font-size: 10.5px; color: #465E78; }
        .fact-label { font-size: 9px; color: #465E78; }
        .fact-value { font-size: 12px; font-weight: bold; color: #0A1F3A; margin-bottom: 9px; }
        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 12px;
            border: 2px solid #0A1F3A;
            font-size: 10px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .status-paid { background-color: #BDF0E4; }
        .status-sent { background-color: #CDE9FA; }
        .status-unpaid { background-color: #FFE066; }
        .status-draft { background-color: #E7EEF5; }
        .status-cancelled { background-color: #FFD6D6; }

        /* Items */
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 26px; }
        .items-table th {
            padding: 0 8px 8px 8px;
            border-bottom: 2px solid #0A1F3A;
            color: #0A1F3A;
            font-size: 10px;
            text-align: left;
            font-weight: bold;
        }
        .items-table td { padding: 14px 8px; border-bottom: 1px solid #CDE2F2; vertical-align: top; }
        .item-no {
            width: 20px; height: 20px;
            border-radius: 10px;
            background-color: #FFE066;
            border: 1px solid #0A1F3A;
            text-align: center;
            line-height: 17px;
            font-size: 9px;
            font-weight: bold;
            color: #0A1F3A;
        }
        .item-desc { font-size: 11.5px; font-weight: bold; color: #0A1F3A; }
        .item-qty { text-align: center; }
        .item-price { text-align: right; color: #465E78; }
        .item-amount { text-align: right; font-weight: bold; }

        /* Footer layout */
        .footer-table { width: 100%; }
        .pay-box { border: 1px solid #0A1F3A; border-radius: 8px; padding: 12px 14px; }
        .bank-item { margin-bottom: 8px; }
        .bank-name { font-size: 10px; font-weight: bold; color: #0A1F3A; }
        .bank-number { font-size: 13px; font-weight: bold; color: #0B66A6; }
        .bank-holder { font-size: 9px; color: #465E78; }
        .qris-img { width: 150px; padding: 4px; background: #ffffff; border: 1px solid #CDE2F2; }
        .qris-caption { font-size: 9px; color: #465E78; margin-top: 4px; }
        .notes-box {
            background-color: #FFF6CC;
            border-left: 4px solid #FFCF0F;
            padding: 10px 14px;
            margin-top: 14px;
        }
        .notes-title { font-size: 9px; font-weight: bold; color: #3F3500; margin-bottom: 4px; }
        .notes-text { font-size: 10px; color: #0A1F3A; line-height: 1.5; }

        .total-label { font-size: 10px; color: #465E78; padding: 0 0 6px 0; }
        .total-value { text-align: right; font-weight: bold; font-size: 11px; padding: 0 0 6px 0; }
        .grand { background-color: #0A1F3A; border-radius: 8px; margin-top: 8px; padding: 14px 16px; }
        .grand-label { font-size: 10px; color: #CDE2F2; }
        .grand-value { font-size: 22px; font-weight: bold; color: #FFCF0F; margin-top: 2px; }

        .signature-box { text-align: center; margin-top: 24px; }
        .signature-img { height: 45px; margin-bottom: 6px; }
        .signature-line { height: 45px; border-bottom: 1px dashed #8DB5D6; width: 140px; margin: 0 auto 8px auto; }
        .signer-name { font-size: 11px; font-weight: bold; }
        .signer-title { font-size: 9px; color: #465E78; }

        /* Short invoices: pin payment, totals and signature to the bottom so the sheet reads as one full A4 page. */
        .anchored { position: absolute; left: 40px; width: 714px; bottom: 62px; }

        .page-foot {
            position: fixed; left: 0; right: 0; bottom: 0; height: 38px;
            background-color: #0A1F3A;
        }
        .page-foot td { color: #ffffff; font-size: 9.5px; padding: 12px 40px 0 40px; }
    </style>
</head>
<body>
    <div class="masthead">
        <table style="width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                    @if($settings->invoice_logo)
                        <img src="{{ $settings->invoice_logo_url }}" style="height: 50px;">
                    @else
                        <div class="logo-text">FK<span>Studio</span></div>
                    @endif
                    <div class="company-name">{{ $companyName }}</div>
                    <div class="company-address">{{ $settings->invoice_company_address ?: 'Jl. Raya Digital No. 21, Jakarta' }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div class="doc-title">Invoice</div>
                    <div class="doc-number">{{ $invoice->invoice_number }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="masthead-rule"></div>

    <div class="container">

        <table class="meta-table">
            <tr>
                <td style="width: 58%; vertical-align: top;">
                    <div class="client-panel">
                        <div class="label">Ditagihkan kepada</div>
                        <div class="client-name">{{ $invoice->client_name }}</div>
                        @if($invoice->client_email)
                            <div class="client-email">{{ $invoice->client_email }}</div>
                        @endif
                        @if($invoice->client_address)
                            <div class="client-address">{!! nl2br(e($invoice->client_address)) !!}</div>
                        @endif
                    </div>
                </td>
                <td style="vertical-align: top; padding-left: 26px;">
                    <div class="fact-label">Tanggal terbit</div>
                    <div class="fact-value">{{ $invoice->issue_date->locale('id')->translatedFormat('d F Y') }}</div>
                    @if($invoice->due_date)
                        <div class="fact-label">Jatuh tempo</div>
                        <div class="fact-value">{{ $invoice->due_date->locale('id')->translatedFormat('d F Y') }}</div>
                    @endif
                    <div class="fact-label" style="margin-bottom: 3px;">Status</div>
                    <div class="status-badge {{ $st['class'] }}">{{ $st['label'] }}</div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 6%;">No</th>
                    <th style="width: 44%;">Deskripsi</th>
                    <th style="text-align: center; width: 10%;">Jml</th>
                    <th style="text-align: right; width: 20%;">Harga</th>
                    <th style="text-align: right; width: 20%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                    <tr>
                        <td><div class="item-no">{{ $loop->iteration }}</div></td>
                        <td><div class="item-desc">{{ $item->description }}</div></td>
                        <td class="item-qty">{{ $item->quantity }}</td>
                        <td class="item-price">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="item-amount">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $anchor = $invoice->items->count() <= 6
                && count($settings->payment_methods ?? []) <= 3
                && mb_strlen((string) $invoice->notes) <= 280;
        @endphp
        <div class="{{ $anchor ? 'anchored' : '' }}">
        <table class="footer-table">
            <tr>
                <td style="width: 56%; vertical-align: top; padding-right: 22px;">
                    @if($invoice->status !== 'Paid' && ($settings->payment_methods || $settings->invoice_qris))
                        <div class="pay-box">
                            <table style="width: 100%;">
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="label">Cara membayar</div>
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
                                        <td style="vertical-align: top; text-align: center; width: 165px;">
                                            <img src="{{ $settings->invoice_qris_url }}" class="qris-img">
                                            <div class="qris-caption">Pindai untuk bayar</div>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        </div>
                    @endif

                    @if($invoice->notes)
                        <div class="notes-box">
                            <div class="notes-title">Catatan</div>
                            <div class="notes-text">{!! nl2br(e($invoice->notes)) !!}</div>
                        </div>
                    @endif
                </td>

                <td style="width: 44%; vertical-align: top;">
                    <table style="width: 100%;">
                        <tr>
                            <td class="total-label">Subtotal</td>
                            <td class="total-value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                            <tr>
                                <td class="total-label">Diskon</td>
                                <td class="total-value">- Rp {{ number_format($discountAmount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->tax > 0)
                            <tr>
                                <td class="total-label">Pajak</td>
                                <td class="total-value">+ Rp {{ number_format($taxAmount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </table>
                    <div class="grand">
                        <div class="grand-label">Total tagihan</div>
                        <div class="grand-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
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
        </div>
    </div>

    <div class="page-foot">
        <table style="width: 100%;">
            <tr>
                <td>Terima kasih atas kepercayaan Anda.</td>
                <td style="text-align: right;">{{ $companyName }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
