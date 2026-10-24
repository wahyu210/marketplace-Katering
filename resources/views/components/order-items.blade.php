@props(['order'])
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Menu</th>
                <th class="num">Harga</th>
                <th class="num">Porsi</th>
                <th class="num">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->menu_name }}</td>
                    <td class="num">{{ rupiah($item->unit_price) }}</td>
                    <td class="num">{{ $item->quantity }}</td>
                    <td class="num">{{ rupiah($item->subtotal) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if ($order->invoice)
    <div class="summary-total" style="border-top:1px solid var(--ink)">
        <div class="row between"><span class="muted">Subtotal</span><span>{{ rupiah($order->invoice->subtotal) }}</span></div>
        <div class="row between"><span class="muted">PPN {{ round(config('katering.tax_rate') * 100) }}%</span><span>{{ rupiah($order->invoice->tax) }}</span></div>
        <div class="row between grand"><span>Total</span><span>{{ rupiah($order->invoice->total) }}</span></div>
    </div>
@endif
