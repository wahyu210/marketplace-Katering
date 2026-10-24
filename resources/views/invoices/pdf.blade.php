<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 32px; }
        .brand { font-size: 18px; font-weight: 700; color: #b5391c; }
        .brand-sub { font-size: 10px; color: #666; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { font-size: 24px; font-weight: 700; color: #1a1a1a; margin-bottom: 4px; }
        .invoice-number { font-size: 14px; font-family: monospace; color: #333; }
        .parties { width: 100%; margin-bottom: 24px; }
        .parties td { vertical-align: top; width: 33%; padding-right: 20px; }
        .party-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 4px; }
        .party-name { font-weight: 700; font-size: 12px; margin-bottom: 2px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { text-align: left; border-bottom: 2px solid #1a1a1a; padding: 8px 6px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        table.items td { padding: 8px 6px; border-bottom: 1px solid #e0e0e0; }
        table.items .num { text-align: right; font-family: monospace; }
        .totals { width: 280px; margin-left: auto; }
        .totals table { width: 100%; border-collapse: collapse; }
        .totals td { padding: 4px 6px; }
        .totals .num { text-align: right; font-family: monospace; }
        .totals .label { color: #666; }
        .totals .grand td { border-top: 2px solid #1a1a1a; font-weight: 700; font-size: 14px; padding-top: 8px; }
        .meta { margin-top: 24px; padding-top: 16px; border-top: 1px solid #ddd; }
        .meta table { width: 100%; }
        .meta td { padding: 2px 0; }
        .meta .label { color: #888; width: 120px; }
        .status { display: inline-block; padding: 2px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
        .status-unpaid { background: #fff3cd; color: #856404; }
        .status-paid { background: #d4edda; color: #155724; }
        .status-void { background: #e2e2e2; color: #666; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <table style="width:100%;margin-bottom:32px">
        <tr>
            <td style="vertical-align:top">
                <div class="brand">Marketplace Katering</div>
                <div class="brand-sub">Platform katering kantor</div>
            </td>
            <td style="vertical-align:top;text-align:right">
                <h1 style="font-size:22px;font-weight:700;margin-bottom:4px">INVOICE</h1>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
            </td>
        </tr>
    </table>

    <table class="parties">
        <tr>
            <td>
                <div class="party-label">Dari (katering)</div>
                <div class="party-name">{{ $invoice->order->merchant->company_name }}</div>
                <div>{{ $invoice->order->merchant->address }}</div>
                <div>{{ $invoice->order->merchant->city }}</div>
                <div>{{ $invoice->order->merchant->phone }}</div>
            </td>
            <td>
                <div class="party-label">Kepada (pemesan)</div>
                <div class="party-name">{{ $invoice->order->customer->office_name }}</div>
                <div>{{ $invoice->order->customer->user->name }}</div>
                <div>{{ $invoice->order->customer->address }}</div>
                <div>{{ $invoice->order->customer->city }}</div>
                <div>{{ $invoice->order->customer->phone }}</div>
            </td>
            <td>
                <div class="party-label">Detail</div>
                <table style="width:100%">
                    <tr><td class="label" style="color:#888">Tanggal terbit</td><td>{{ tanggal($invoice->issued_at) }}</td></tr>
                    <tr><td class="label" style="color:#888">Jatuh tempo</td><td style="font-weight:700">{{ tanggal($invoice->due_date) }}</td></tr>
                    <tr><td class="label" style="color:#888">Status</td><td><span class="status status-{{ $invoice->status->value }}">{{ $invoice->status->label() }}</span></td></tr>
                    <tr><td class="label" style="color:#888">No. pesanan</td><td style="font-family:monospace">{{ $invoice->order->order_number }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width:45%">Menu</th>
                <th class="num">Harga satuan</th>
                <th class="num">Porsi</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->order->items as $item)
                <tr>
                    <td>{{ $item->menu_name }}</td>
                    <td class="num">{{ rupiah($item->unit_price) }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ rupiah($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr><td class="label">Subtotal</td><td class="num">{{ rupiah($invoice->subtotal) }}</td></tr>
            <tr><td class="label">PPN {{ round(config('katering.tax_rate') * 100) }}%</td><td class="num">{{ rupiah($invoice->tax) }}</td></tr>
            <tr class="grand"><td>Total</td><td class="num">{{ rupiah($invoice->total) }}</td></tr>
        </table>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td class="label">Pengiriman</td>
                <td>{{ tanggal($invoice->order->delivery_date, true) }}, pukul {{ $invoice->order->deliveryTimeLabel() }} WIB</td>
            </tr>
            <tr>
                <td class="label">Alamat kirim</td>
                <td>{{ $invoice->order->delivery_address }}</td>
            </tr>
            @if ($invoice->paid_at)
                <tr>
                    <td class="label">Dibayar pada</td>
                    <td>{{ tanggal($invoice->paid_at) }}</td>
                </tr>
            @endif
        </table>
    </div>

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh {{ config('app.name') }}. Tidak memerlukan tanda tangan.
    </div>
</body>
</html>
