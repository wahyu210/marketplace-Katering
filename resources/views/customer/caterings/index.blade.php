<x-layouts.portal title="Cari katering">
    <header class="page-head">
        <div>
            <h1>Cari katering</h1>
            <p>{{ $merchants->total() }} katering siap menerima pesanan kantor.</p>
        </div>
    </header>

    <form method="GET" class="filters" role="search" id="catering-search">
        <label class="field grow" style="min-width:220px">
            <span class="label">Kata kunci</span>
            <input class="input" type="search" name="q" id="search-q" value="{{ $filters['q'] ?? '' }}" placeholder="Nama katering atau menu, mis. rendang">
        </label>
        <label class="field">
            <span class="label">Lokasi</span>
            <select class="select" name="city" id="search-city">
                <option value="">Semua kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city }}" @selected(($filters['city'] ?? '') === $city)>{{ $city }}{{ $city === $customerCity ? ' (kota Anda)' : '' }}</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            <span class="label">Jenis makanan</span>
            <select class="select" name="category" id="search-category">
                <option value="">Semua jenis</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(($filters['category'] ?? '') === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            <span class="label">Harga maks. / porsi</span>
            <select class="select" name="max_price" id="search-price">
                <option value="">Berapa saja</option>
                @foreach ([25000, 35000, 50000, 75000] as $p)
                    <option value="{{ $p }}" @selected((int) ($filters['max_price'] ?? 0) === $p)>≤ {{ rupiah($p) }}</option>
                @endforeach
            </select>
        </label>
        <button class="btn btn-sm" style="height:38px" id="btn-search">Cari</button>
        @if (array_filter($filters))
            <a href="{{ route('customer.caterings.index') }}" class="btn btn-ghost btn-sm" style="height:38px">Reset</a>
        @endif
    </form>

    @if ($merchants->isEmpty())
        <div class="panel empty">
            <h3>Tidak ada katering yang cocok</h3>
            <p>Coba hapus salah satu filter atau gunakan kata kunci lain.</p>
            <a href="{{ route('customer.caterings.index') }}" class="btn btn-line">Tampilkan semua</a>
        </div>
    @else
        <div class="caterer-list">
            @foreach ($merchants as $merchant)
                @php $cover = $merchant->menus->first(); @endphp
                <a href="{{ route('customer.caterings.show', $merchant) }}" class="caterer" id="caterer-{{ $merchant->slug }}">
                    <div class="caterer-photo">
                        @if ($cover)<img src="{{ $cover->photoUrl() }}" alt="{{ $cover->name }}" loading="lazy">@endif
                    </div>
                    <div>
                        <h2 class="caterer-name">{{ $merchant->company_name }}</h2>
                        <div class="caterer-meta">
                            <span>{{ $merchant->city }}</span>
                            <span>{{ $merchant->menus_count }} menu</span>
                            @if ($merchant->city === $customerCity)<span style="color:var(--green)">Satu kota dengan kantor Anda</span>@endif
                        </div>
                        <p class="caterer-desc">{{ $merchant->description }}</p>
                        <div class="caterer-menus">
                            @foreach ($merchant->menus as $menu)
                                <span class="chip">{{ $menu->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="caterer-price">
                        <span class="muted xs">mulai</span>
                        <b>{{ rupiah($merchant->menus_min_price) }}</b>
                        <span class="muted xs">per porsi</span>
                    </div>
                </a>
            @endforeach
        </div>
        {{ $merchants->links() }}
    @endif
</x-layouts.portal>
