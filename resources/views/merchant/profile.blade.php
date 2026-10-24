<x-layouts.portal title="Profil katering">
    <header class="page-head">
        <div>
            <h1>Profil katering</h1>
            <p>Informasi ini tampil ke kantor saat mereka mencari dan memesan.</p>
        </div>
    </header>

    <form method="POST" action="{{ route('merchant.profile.update') }}" enctype="multipart/form-data" novalidate class="panel" style="max-width:760px">
        @csrf
        @method('PUT')
        <div class="panel-pad">
            <div class="field">
                <span class="label">Logo <span class="opt">(opsional)</span></span>
                <div class="row" style="--gap:18px;align-items:flex-start">
                    <div class="upload-preview is-square" id="logo-preview">
                        @if ($merchant->logoUrl())
                            <img src="{{ $merchant->logoUrl() }}" alt="Logo {{ $merchant->company_name }}">
                        @else
                            <span class="serif" style="font-size:32px;color:var(--ink)">{{ $merchant->initials() }}</span>
                        @endif
                    </div>
                    <div class="grow">
                        <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/webp" data-preview="#logo-preview">
                        <div class="hint">JPG, PNG, atau WEBP, maks. 2 MB. Persegi lebih baik.</div>
                        @if ($merchant->logo)
                            <label class="check small" style="margin-top:10px"><input type="checkbox" name="remove_logo" value="1"> Hapus logo</label>
                        @endif
                        @error('logo')<div class="error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <hr>

            <label class="field">
                <span class="label">Nama katering</span>
                <input class="input @error('company_name') is-invalid @enderror" name="company_name" id="company_name" value="{{ old('company_name', $merchant->company_name) }}" required>
                @error('company_name')<div class="error">{{ $message }}</div>@enderror
            </label>

            <label class="field">
                <span class="label">Deskripsi</span>
                <textarea class="textarea @error('description') is-invalid @enderror" name="description" id="description" rows="5" placeholder="Contoh: Katering rumahan khas Minang sejak 2012. Melayani 50–500 porsi per hari, bersertifikat halal, gratis ongkir area Jakarta Selatan.">{{ old('description', $merchant->description) }}</textarea>
                <div class="hint">Jelaskan spesialisasi, kapasitas harian, area pengiriman, dan sertifikasi.</div>
                @error('description')<div class="error">{{ $message }}</div>@enderror
            </label>

            <div class="grid-2 field">
                <label class="field">
                    <span class="label">Nama penanggung jawab</span>
                    <input class="input @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </label>
                <label class="field">
                    <span class="label">Kontak (telepon / WhatsApp)</span>
                    <input class="input @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone', $merchant->phone) }}" required inputmode="tel">
                    @error('phone')<div class="error">{{ $message }}</div>@enderror
                </label>
            </div>

            <label class="field">
                <span class="label">Kota</span>
                <select class="select @error('city') is-invalid @enderror" name="city" id="city" required>
                    @foreach ($cities as $city)
                        <option value="{{ $city }}" @selected(old('city', $merchant->city) === $city)>{{ $city }}</option>
                    @endforeach
                </select>
                @error('city')<div class="error">{{ $message }}</div>@enderror
            </label>

            <label class="field">
                <span class="label">Alamat dapur</span>
                <textarea class="textarea @error('address') is-invalid @enderror" name="address" id="address" rows="2" required style="min-height:72px">{{ old('address', $merchant->address) }}</textarea>
                @error('address')<div class="error">{{ $message }}</div>@enderror
            </label>

            <div class="field">
                <span class="label">Email akun</span>
                <div class="mono small">{{ $user->email }}</div>
            </div>
        </div>
        <div class="panel-foot row between">
            <span class="muted xs">Terakhir diubah {{ $merchant->updated_at->diffForHumans() }}</span>
            <button type="submit" class="btn" id="btn-save-profile">Simpan profil</button>
        </div>
    </form>
</x-layouts.portal>
