<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 20px; }
        .branding { font-size: 24px; font-weight: bold; color: #000; text-transform: uppercase; }
        .invoice-title { text-align: right; font-size: 28px; font-weight: 100; color: #777; margin-top: -35px; }
        .info-table { width: 100%; margin-bottom: 40px; }
        .info-table td { vertical-align: top; width: 50%; }
        .label { font-size: 10px; color: #777; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .value { font-size: 12px; font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { background: #f5f5f5; text-align: left; padding: 10px; font-size: 11px; border-bottom: 1px solid #ddd; text-transform: uppercase; }
        .items-table td { padding: 10px; font-size: 11px; border-bottom: 1px solid #eee; }
        .total-section { text-align: right; }
        .total-row { font-size: 14px; font-weight: bold; margin-top: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="branding">{{ $brandingName }}</div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <table class="info-table">
        <tr>
            <td>
                <div class="label">Billed To:</div>
                <div class="value">{{ $order->customer_name }}</div>
                <div class="value" style="font-weight: normal;">{{ $order->customer_email }}</div>
                <div class="value" style="font-weight: normal;">{{ $order->customer_phone }}</div>
                <div class="value" style="font-weight: normal; margin-top: 5px;">{{ $order->customer_address }}</div>
            </td>
            <td style="text-align: right;">
                <div class="label">Invoice Details:</div>
                <div class="value">No: #{{ $order->order_number }}</div>
                <div class="value">Date: {{ $order->created_at->format('d M Y') }}</div>
                <div class="value">Payment: {{ strtoupper(str_replace('_', ' ', $order->payment_method)) }}</div>
                <div class="value">Status: {{ strtoupper($order->status) }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            TOTAL AMOUNT: Rp {{ number_format($order->total_amount, 0, ',', '.') }}
        </div>
    </div>

    <div class="footer">
        Generated automatically by {{ $brandingName }} - Thank you for your business!
    </div>
</body>
</html>
