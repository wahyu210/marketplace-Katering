<x-layouts.portal title="Pesanan & invoice">
    <header class="page-head">
        <div>
            <h1>Pesanan & invoice</h1>
            <p>Semua pesanan masuk dari kantor beserta status invoicenya.</p>
        </div>
    </header>

    <nav class="tabs" aria-label="Status pesanan">
        <a href="{{ route('merchant.orders.index', array_filter(['from' => $filters['from'] ?? null, 'to' => $filters['to'] ?? null, 'q' => $filters['q'] ?? null])) }}" @class(['is-active' => empty($filters['status'])])>Semua</a>
        @foreach ($statuses as $s)
            <a href="{{ route('merchant.orders.index', array_filter(['status' => $s->value, 'from' => $filters['from'] ?? null, 'to' => $filters['to'] ?? null, 'q' => $filters['q'] ?? null])) }}" @class(['is-active' => ($filters['status'] ?? null) === $s->value])>{{ $s->label() }}</a>
        @endforeach
    </nav>

    <form method="GET" class="filters" role="search">
        @if (! empty($filters['status']))<input type="hidden" name="status" value="{{ $filters['status'] }}">@endif
        <label class="field grow" style="min-width:200px">
            <span class="label">Cari</span>
            <input class="input" type="search" name="q" id="order-q" value="{{ $filters['q'] ?? '' }}" placeholder="No. pesanan atau nama kantor">
        </label>
        <label class="field">
            <span class="label">Kirim dari</span>
            <input class="input" type="date" name="from" id="order-from" value="{{ $filters['from'] ?? '' }}">
        </label>
        <label class="field">
            <span class="label">sampai</span>
            <input class="input" type="date" name="to" id="order-to" value="{{ $filters['to'] ?? '' }}">
        </label>
        <button class="btn btn-sm" style="height:38px" id="btn-filter-orders">Terapkan</button>
        @if (array_filter($filters))
            <a href="{{ route('merchant.orders.index') }}" class="btn btn-ghost btn-sm" style="height:38px">Reset</a>
        @endif
    </form>

    <div class="panel">
        @if ($orders->isEmpty())
            <div class="empty">
                <h3>Tidak ada pesanan</h3>
                <p>{{ array_filter($filters) ? 'Tidak ada pesanan yang cocok dengan filter.' : 'Pesanan dari kantor akan muncul di sini.' }}</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Kantor</th>
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
                                    <a class="row-link mono" href="{{ route('merchant.orders.show', $order) }}">{{ $order->order_number }}</a>
                                    <div class="muted xs">dipesan {{ $order->created_at->diffForHumans() }}</div>
                                </td>
                                <td>{{ $order->customer->office_name }}</td>
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
