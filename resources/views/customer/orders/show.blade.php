<x-layouts.portal :title="'Pesanan '.$order->order_number">
    <header class="page-head">
        <div>
            <a href="{{ route('customer.orders.index') }}" class="crumb">← Pesanan</a>
            <h1 style="font-family:var(--font-mono);font-size:28px">{{ $order->order_number }}</h1>
            <p>Dipesan {{ tanggal($order->created_at) }} dari <a href="{{ route('customer.caterings.show', $order->merchant) }}">{{ $order->merchant->company_name }}</a></p>
        </div>
        <x-status :status="$order->status" />
    </header>

    <x-order-timeline :order="$order" />

    <div class="order-layout" style="margin-top:28px">
        <div class="stack" style="--gap:24px">
            <section class="panel">
                <div class="panel-head"><h2>Rincian pesanan</h2><span class="muted small">{{ $order->totalPortions() }} porsi</span></div>
                <x-order-items :order="$order" />
            </section>

            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:16px">Pengiriman</h2>
                <dl class="kv">
                    <dt>Tanggal</dt><dd><strong>{{ tanggal($order->delivery_date, true) }}</strong></dd>
                    <dt>Jam tiba</dt><dd class="mono">{{ $order->deliveryTimeLabel() }} WIB</dd>
                    <dt>Alamat</dt><dd>{{ $order->delivery_address }}</dd>
                    <dt>Catatan</dt><dd>{{ $order->notes ?: '—' }}</dd>
                </dl>
            </section>
        </div>

        <aside class="stack" style="--gap:24px">
            @if ($order->invoice)
                <section class="panel panel-pad">
                    <div class="row between" style="margin-bottom:12px">
                        <h2 style="font-size:15px">Invoice</h2>
                        <x-status :status="$order->invoice->status" />
                    </div>
                    <div class="mono" style="font-size:15px">{{ $order->invoice->invoice_number }}</div>
                    <div class="muted small" style="margin-bottom:16px">Jatuh tempo {{ tanggal($order->invoice->due_date) }}</div>
                    <div class="stack" style="--gap:8px">
                        <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-block" id="btn-view-invoice">Lihat invoice</a>
                        <a href="{{ route('invoices.pdf', $order->invoice) }}" class="btn btn-line btn-block" id="btn-download-invoice">Unduh PDF</a>
                    </div>
                </section>
            @endif

            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:12px">Katering</h2>
                <div><strong>{{ $order->merchant->company_name }}</strong></div>
                <div class="small muted">{{ $order->merchant->city }}</div>
                <div class="small mono" style="margin-top:8px">{{ $order->merchant->phone }}</div>
            </section>

            @if ($order->status === \App\Enums\OrderStatus::Pending)
                <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" data-confirm="Batalkan pesanan ini? Invoice juga akan dibatalkan.">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-danger btn-block" id="btn-cancel-order">Batalkan pesanan</button>
                    <p class="muted xs" style="margin:8px 0 0">Bisa dibatalkan selama belum dikonfirmasi katering.</p>
                </form>
            @endif
        </aside>
    </div>
</x-layouts.portal>
