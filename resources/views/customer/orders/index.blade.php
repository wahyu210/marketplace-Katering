<x-layouts.portal title="Pesanan & invoice">
    <header class="page-head">
        <div>
            <h1>Pesanan & invoice</h1>
            <p>Riwayat pesanan kantor Anda dan invoice dari setiap katering.</p>
        </div>
        <a href="{{ route('customer.caterings.index') }}" class="btn" id="btn-new-order">Pesan lagi</a>
    </header>

    <nav class="tabs" aria-label="Status pesanan">
        <a href="{{ route('customer.orders.index') }}" @class(['is-active' => ! $status])>Semua</a>
        @foreach ($statuses as $s)
            <a href="{{ route('customer.orders.index', ['status' => $s->value]) }}" @class(['is-active' => $status === $s->value])>{{ $s->label() }}</a>
        @endforeach
    </nav>

    <div class="panel">
        @if ($orders->isEmpty())
            <div class="empty">
                <h3>Belum ada pesanan</h3>
                <p>Cari katering, pilih menu dan jumlah porsi, invoice akan langsung terbit.</p>
                <a href="{{ route('customer.caterings.index') }}" class="btn">Cari katering</a>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Katering</th>
                            <th>Kirim</th>
                            <th class="num">Porsi</th>
                            <th>Status</th>
                            <th>Invoice</th>
                            <th class="num">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="nowrap">
                                    <a class="row-link mono" href="{{ route('customer.orders.show', $order) }}">{{ $order->order_number }}</a>
                                    <div class="muted xs">{{ tanggal($order->created_at) }}</div>
                                </td>
                                <td>{{ $order->merchant->company_name }}</td>
                                <td class="nowrap">{{ tanggal($order->delivery_date) }}<div class="muted xs mono">{{ $order->deliveryTimeLabel() }}</div></td>
                                <td class="num">{{ (int) $order->items_sum_quantity }}</td>
                                <td><x-status :status="$order->status" /></td>
                                <td class="nowrap">
                                    @if ($order->invoice)
                                        <a href="{{ route('invoices.show', $order->invoice) }}" class="mono small">{{ $order->invoice->invoice_number }}</a>
                                        <div><x-status :status="$order->invoice->status" /></div>
                                    @endif
                                </td>
                                <td class="num">{{ rupiah($order->invoice?->total ?? $order->subtotal) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    {{ $orders->links() }}
</x-layouts.portal>
