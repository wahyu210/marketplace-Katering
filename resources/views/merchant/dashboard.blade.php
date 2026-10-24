<x-layouts.portal title="Ringkasan">
    <header class="page-head">
        <div>
            <h1>Selamat datang, {{ $merchant->company_name }}</h1>
            <p>{{ tanggal(today(), true) }}</p>
        </div>
        <a href="{{ route('merchant.menus.create') }}" class="btn" id="btn-add-menu">Tambah menu</a>
    </header>

    @if ($profileIncomplete || $stats['menus'] === 0)
        <div class="panel panel-pad" style="margin-bottom:24px;border-color:var(--ink)">
            <strong>Agar katering Anda muncul di pencarian kantor:</strong>
            <ol class="small" style="margin:8px 0 0;padding-left:18px">
                @if ($profileIncomplete)<li><a href="{{ route('merchant.profile.edit') }}">Lengkapi deskripsi profil</a></li>@endif
                @if ($stats['menus'] === 0)<li><a href="{{ route('merchant.menus.create') }}">Tambahkan minimal satu menu yang tersedia</a></li>@endif
            </ol>
        </div>
    @endif

    <div class="stats">
        <div @class(['stat', 'is-hot' => $stats['pending'] > 0])>
            <div class="upper">Perlu konfirmasi</div>
            <b>{{ $stats['pending'] }}</b>
            <a href="{{ route('merchant.orders.index', ['status' => 'pending']) }}">Lihat pesanan</a>
        </div>
        <div class="stat">
            <div class="upper">Kirim hari ini</div>
            <b>{{ $stats['today'] }}</b>
            <a href="{{ route('merchant.orders.index', ['from' => today()->toDateString(), 'to' => today()->toDateString()]) }}">Lihat jadwal</a>
        </div>
        <div class="stat">
            <div class="upper">Invoice belum dibayar</div>
            <b>{{ rupiah($stats['unpaid']) }}</b>
        </div>
        <div class="stat">
            <div class="upper">Diterima bulan ini</div>
            <b>{{ rupiah($stats['revenue_month']) }}</b>
        </div>
    </div>

    <section class="panel" style="margin-top:28px">
        <div class="panel-head">
            <h2>Pengiriman mendatang</h2>
            <a href="{{ route('merchant.orders.index') }}" class="small">Semua pesanan</a>
        </div>
        @if ($upcoming->isEmpty())
            <div class="empty">
                <h3>Belum ada jadwal kirim</h3>
                <p>Pesanan baru dari kantor akan tampil di sini, diurutkan dari tanggal kirim terdekat.</p>
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tanggal kirim</th>
                            <th>Kantor</th>
                            <th class="num">Porsi</th>
                            <th class="num">Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($upcoming as $order)
                            <tr>
                                <td class="nowrap">
                                    <a class="row-link" href="{{ route('merchant.orders.show', $order) }}">{{ tanggal($order->delivery_date, true) }}</a>
                                    <div class="muted xs mono">{{ $order->deliveryTimeLabel() }} · {{ $order->order_number }}</div>
                                </td>
                                <td>{{ $order->customer->office_name }}<div class="muted xs">{{ $order->customer->city }}</div></td>
                                <td class="num">{{ $order->totalPortions() }}</td>
                                <td class="num">{{ rupiah($order->subtotal) }}</td>
                                <td><x-status :status="$order->status" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</x-layouts.portal>
