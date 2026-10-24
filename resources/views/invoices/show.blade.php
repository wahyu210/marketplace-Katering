<x-layouts.portal :title="'Invoice '.$invoice->invoice_number">
    <header class="page-head">
        <div>
            <a href="{{ auth()->user()->isMerchant()
                ? route('merchant.orders.show', $invoice->order)
                : route('customer.orders.show', $invoice->order) }}" class="crumb">← Kembali ke pesanan</a>
            <h1 class="mono" style="font-family:var(--font-mono);font-size:28px">{{ $invoice->invoice_number }}</h1>
            <p>Diterbitkan {{ tanggal($invoice->issued_at) }}</p>
        </div>
        <x-status :status="$invoice->status" />
    </header>

    <div class="order-layout" style="margin-top:28px">
        <div class="stack" style="--gap:24px">
            <section class="panel">
                <div class="panel-head">
                    <h2>Rincian pesanan</h2>
                    <span class="muted small">{{ $invoice->order->order_number }}</span>
                </div>
                <x-order-items :order="$invoice->order" />
            </section>

            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:16px">Pengiriman</h2>
                <dl class="kv">
                    <dt>Tanggal</dt><dd><strong>{{ tanggal($invoice->order->delivery_date, true) }}</strong></dd>
                    <dt>Jam tiba</dt><dd class="mono">{{ $invoice->order->deliveryTimeLabel() }} WIB</dd>
                    <dt>Alamat</dt><dd>{{ $invoice->order->delivery_address }}</dd>
                </dl>
            </section>
        </div>

        <aside class="stack" style="--gap:24px">
            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:16px">Detail invoice</h2>
                <dl class="kv">
                    <dt>Nomor invoice</dt><dd class="mono">{{ $invoice->invoice_number }}</dd>
                    <dt>Tanggal terbit</dt><dd>{{ tanggal($invoice->issued_at) }}</dd>
                    <dt>Jatuh tempo</dt><dd><strong>{{ tanggal($invoice->due_date) }}</strong></dd>
                    <dt>Status</dt><dd><x-status :status="$invoice->status" /></dd>
                    @if ($invoice->paid_at)
                        <dt>Dibayar</dt><dd>{{ tanggal($invoice->paid_at) }}</dd>
                    @endif
                </dl>
            </section>

            <section class="panel panel-pad">
                <h2 style="font-size:15px;margin-bottom:12px">Pihak terkait</h2>
                <div style="margin-bottom:16px">
                    <div class="muted xs" style="margin-bottom:4px">Katering</div>
                    <div><strong>{{ $invoice->order->merchant->company_name }}</strong></div>
                    <div class="small">{{ $invoice->order->merchant->address }}</div>
                    <div class="small mono">{{ $invoice->order->merchant->phone }}</div>
                </div>
                <div>
                    <div class="muted xs" style="margin-bottom:4px">Pemesan</div>
                    <div><strong>{{ $invoice->order->customer->office_name }}</strong></div>
                    <div class="small">{{ $invoice->order->customer->user->name }}</div>
                    <div class="small mono">{{ $invoice->order->customer->phone }}</div>
                </div>
            </section>

            <div class="stack" style="--gap:8px">
                <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-line btn-block" id="btn-download-pdf">Unduh PDF</a>
                @can('markPaid', $invoice)
                    <form method="POST" action="{{ route('invoices.paid', $invoice) }}" data-confirm="Tandai invoice ini sudah dibayar?">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-accent btn-block" id="btn-mark-paid">Tandai lunas</button>
                    </form>
                @endcan
            </div>
        </aside>
    </div>
</x-layouts.portal>
