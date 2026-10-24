<x-layouts.portal title="Menu">
    <header class="page-head">
        <div>
            <h1>Menu</h1>
            <p>{{ $menus->total() }} menu · menu nonaktif tidak tampil ke kantor.</p>
        </div>
        <div class="row">
            <form method="GET" class="row" style="--gap:8px" role="search">
                <label class="sr-only" for="q">Cari menu</label>
                <input class="input" style="height:40px;width:220px" type="search" name="q" id="q" value="{{ request('q') }}" placeholder="Cari nama menu">
            </form>
            <a href="{{ route('merchant.menus.create') }}" class="btn" id="btn-new-menu">Tambah menu</a>
        </div>
    </header>

    @if ($menus->isEmpty())
        <div class="panel empty">
            @if (request('q'))
                <h3>Tidak ada menu "{{ request('q') }}"</h3>
                <p><a href="{{ route('merchant.menus.index') }}">Tampilkan semua menu</a></p>
            @else
                <h3>Belum ada menu</h3>
                <p>Tambahkan menu dengan foto, deskripsi, dan harga per porsi agar kantor bisa memesan.</p>
                <a href="{{ route('merchant.menus.create') }}" class="btn">Tambah menu pertama</a>
            @endif
        </div>
    @else
        <div class="menu-grid">
            @foreach ($menus as $menu)
                <article @class(['menu-card', 'is-off' => ! $menu->is_available])>
                    <div class="menu-card-photo">
                        <img src="{{ $menu->photoUrl() }}" alt="{{ $menu->name }}" loading="lazy">
                        @unless ($menu->is_available)<span class="tag tag-muted">Nonaktif</span>@endunless
                    </div>
                    <div class="menu-card-body">
                        <span class="xs muted">{{ $menu->category->name }}</span>
                        <h3>{{ $menu->name }}</h3>
                        <p>{{ $menu->description }}</p>
                        <div class="menu-card-foot">
                            <span class="price">{{ rupiah($menu->price) }} <small>/ porsi</small></span>
                            <span class="xs muted">min. {{ $menu->min_order }}</span>
                        </div>
                        <div class="row" style="--gap:6px;margin-top:8px">
                            <a href="{{ route('merchant.menus.edit', $menu) }}" class="btn btn-line btn-sm grow" id="edit-menu-{{ $menu->id }}">Edit</a>
                            <form method="POST" action="{{ route('merchant.menus.destroy', $menu) }}" data-confirm="Hapus menu &quot;{{ $menu->name }}&quot;? Riwayat pesanan tetap tersimpan.">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" id="delete-menu-{{ $menu->id }}">Hapus</button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        {{ $menus->links() }}
    @endif
</x-layouts.portal>
