@php $editing = $menu->exists; @endphp
<x-layouts.portal :title="$editing ? 'Edit menu' : 'Tambah menu'">
    <header class="page-head">
        <div>
            <a href="{{ route('merchant.menus.index') }}" class="crumb">← Menu</a>
            <h1>{{ $editing ? 'Edit: '.$menu->name : 'Tambah menu' }}</h1>
        </div>
    </header>

    <form method="POST"
          action="{{ $editing ? route('merchant.menus.update', $menu) : route('merchant.menus.store') }}"
          enctype="multipart/form-data" novalidate class="panel" style="max-width:820px">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="panel-pad">
            <div class="field">
                <span class="label">Foto menu</span>
                <div class="upload">
                    <div class="upload-preview" id="photo-preview">
                        @if ($editing)
                            <img src="{{ $menu->photoUrl() }}" alt="{{ $menu->name }}">
                        @else
                            <span>Belum ada foto</span>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="photo" id="photo" accept="image/png,image/jpeg,image/webp" data-preview="#photo-preview" @unless($editing) required @endunless>
                        <div class="hint">JPG, PNG, atau WEBP, maks. 4 MB. Foto dari atas dengan cahaya alami paling menarik.
                            @if ($editing) Kosongkan jika tidak ingin mengganti foto. @endif</div>
                        @error('photo')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            <div class="grid-2 field">
                <label class="field">
                    <span class="label">Nama menu</span>
                    <input class="input @error('name') is-invalid @enderror" name="name" id="menu-name" value="{{ old('name', $menu->name) }}" required placeholder="Nasi Box Ayam Bakar">
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </label>
                <label class="field">
                    <span class="label">Jenis makanan</span>
                    <select class="select @error('category_id') is-invalid @enderror" name="category_id" id="category_id" required>
                        <option value="">Pilih jenis</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $menu->category_id) === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="error">{{ $message }}</div>@enderror
                </label>
            </div>

            <label class="field">
                <span class="label">Deskripsi</span>
                <textarea class="textarea @error('description') is-invalid @enderror" name="description" id="menu-description" rows="3" required placeholder="Isi menu, lauk pendamping, kemasan, dan info alergen.">{{ old('description', $menu->description) }}</textarea>
                @error('description')<div class="error">{{ $message }}</div>@enderror
            </label>

            <div class="grid-2 field">
                <label class="field">
                    <span class="label">Harga per porsi</span>
                    <div class="input-prefix">
                        <span>Rp</span>
                        <input class="input @error('price') is-invalid @enderror" type="number" name="price" id="price" min="1000" step="500" value="{{ old('price', $menu->price) }}" required inputmode="numeric" placeholder="35000">
                    </div>
                    @error('price')<div class="error">{{ $message }}</div>@enderror
                </label>
                <label class="field">
                    <span class="label">Minimal order (porsi)</span>
                    <input class="input mono @error('min_order') is-invalid @enderror" type="number" name="min_order" id="min_order" min="1" value="{{ old('min_order', $menu->min_order) }}" required inputmode="numeric">
                    @error('min_order')<div class="error">{{ $message }}</div>@enderror
                </label>
            </div>

            <div class="field">
                <input type="hidden" name="is_available" value="0">
                <label class="check">
                    <input type="checkbox" name="is_available" id="is_available" value="1" @checked(old('is_available', $menu->is_available))>
                    Tersedia untuk dipesan
                </label>
            </div>
        </div>
        <div class="panel-foot row between">
            <a href="{{ route('merchant.menus.index') }}" class="btn btn-ghost">Batal</a>
            <button type="submit" class="btn" id="btn-save-menu">{{ $editing ? 'Simpan perubahan' : 'Tambah menu' }}</button>
        </div>
    </form>
</x-layouts.portal>
