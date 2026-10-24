<x-layouts.portal :title="'Pesanan '.$order->order_number">
    <header class="page-head">
        <div>
            <a href="{{ route('merchant.orders.index') }}" class="crumb">← Pesanan</a>
            <h1 class="mono" style="font-family:var(--font-mono);font-size:28px">{{ $order->order_number }}</h1>
            <p>Dipesan {{ tanggal($order->created_at) }} pukul {{ $order->created_at->format('H:i') }} oleh {{ $order->customer->office_name }}</p>
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
            @php $next = $order->status->nextForMerchant(); @endphp
            @if ($next)
                <section class="panel panel-pad">
                    <h2 style="font-size:15px;margin-bottom:6px">Tindakan</h2>
                    <p class="muted small" style="margin:0 0 16px">
                        @if ($order->status === \App\Enums\OrderStatus::Pending)
                            Konfirmasi jika kapasitas dapur tersedia di tanggal tersebut.
                        @else
                            Tandai terkirim setelah pesanan diterima kantor.
                        @endif
                    </p>
                    <div class="stack" style="--gap:8px">
                        @foreach ($next as $status)
                            <form method="POST" action="{{ route('merchant.orders.status', $order) }}"
                                  @if ($status === \App\Enums\OrderStatus::Cancelled) data-confirm="Batalkan pesanan ini? Invoice akan dibatalkan." @endif>
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $status->value }}">
                                <button type="submit" id="btn-status-{{ $status->value }}"
                                        @class(['btn btn-block', 'btn-danger' => $status === \App\Enums\OrderStatus::Cancelled, 'btn-accent' => $status !== \App\Enums\OrderStatus::Cancelled])>
                                    {{ match ($status) {
                                        \App\Enums\OrderStatus::Confirmed => 'Konfirmasi pesanan',
                                        \App\Enums\OrderStatus::Delivered => 'Tandai terkirim',
                                        \App\Enums\OrderStatus::Cancelled => 'Tolak / batalkan',
                                        default => $status->label(),
                                    } }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($order->invoice)
                <section class="panel panel-pad">
                    <div class="row between" style="margin-bottom:12px">
                        <h2 style="font-size:15px">Invoice</h2>
                        <x-status :status="$order->invoice->status" />
                    </div>
                    <div class="mono" style="font-size:15px">{{ $order->invoice->invoice_number }}</div>
                    <div class="muted small" style="margin-bottom:16px">Jatuh tempo {{ tanggal($order->invoice->due_date) }}</div>
                    <div class="stack" style="--gap:8px">
                        <a href="{{ route('invoices.show', $order->invoice) }}" class="btn btn-line btn-block" id="btn-view-invoice">Lihat invoice</a>
                        @can('markPaid', $order->invoice)
                            <form method="POST" action="{{ route('invoices.paid', $order->invoice) }}" data-confirm="Tandai invoice ini sudah dibayar?">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-block" id="btn-mark-paid">Tandai lunas</button>
                            </form>
                        @endcan
                    </div>
                </section>
            @endif

            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:12px">Pemesan</h2>
                <div><strong>{{ $order->customer->office_name }}</strong></div>
                <div class="small">{{ $order->customer->user->name }}</div>
                <div class="small mono" style="margin-top:8px">{{ $order->customer->phone }}</div>
                <div class="small"><a href="mailto:{{ $order->customer->user->email }}">{{ $order->customer->user->email }}</a></div>
            </section>
        </aside>
    </div>
</x-layouts.portal>
