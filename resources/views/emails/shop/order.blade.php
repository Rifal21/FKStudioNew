@php $site = \App\Models\SiteSetting::current(); @endphp
<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><title>{{ $subjectLine }}</title></head>
<body style="margin:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif;color:#0b1b2b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:2px solid #0b1b2b;border-radius:12px;">
                <tr><td style="background:#ffcf0f;padding:18px 24px;border-bottom:2px solid #0b1b2b;border-radius:10px 10px 0 0;font-weight:bold;font-size:18px;">
                    {{ $site->site_name ?: 'FKStudio' }}
                </td></tr>
                <tr><td style="padding:24px;">
                    <h1 style="margin:0 0 16px;font-size:20px;">{{ $subjectLine }}</h1>
                    @if ($customerName)
                        <p style="margin:0 0 12px;font-size:15px;line-height:1.5;">Halo {{ $customerName }},</p>
                    @endif
                    @foreach ($lines as $line)
                        <p style="margin:0 0 12px;font-size:15px;line-height:1.5;">{{ $line }}</p>
                    @endforeach
                    @if ($action)
                        <p style="margin:24px 0 8px;">
                            <a href="{{ $action['url'] }}" style="display:inline-block;background:#1e9be5;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 20px;border-radius:8px;">{{ $action['label'] }}</a>
                        </p>
                    @endif
                    <p style="margin:24px 0 0;font-size:13px;color:#5b6b7b;">Nomor pesanan: {{ $order->order_number }}</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
