<x-layouts.portal title="Profil kantor">
    <header class="page-head">
        <div>
            <h1>Profil kantor</h1>
            <p>Alamat ini dipakai sebagai alamat pengiriman bawaan.</p>
        </div>
    </header>

    <form method="POST" action="{{ route('customer.profile.update') }}" novalidate class="panel" style="max-width:720px">
        @csrf
        @method('PUT')
        <div class="panel-pad">
            <label class="field">
                <span class="label">Nama kantor / perusahaan</span>
                <input class="input @error('office_name') is-invalid @enderror" name="office_name" id="office_name" value="{{ old('office_name', $customer->office_name) }}" required>
                @error('office_name')<div class="error">{{ $message }}</div>@enderror
            </label>
            <div class="grid-2 field">
                <label class="field">
                    <span class="label">Nama penanggung jawab</span>
                    <input class="input @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="error">{{ $message }}</div>@enderror
                </label>
                <label class="field">
                    <span class="label">Telepon</span>
                    <input class="input @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" required inputmode="tel">
                    @error('phone')<div class="error">{{ $message }}</div>@enderror
                </label>
            </div>
            <label class="field">
                <span class="label">Kota</span>
                <select class="select @error('city') is-invalid @enderror" name="city" id="city" required>
                    @foreach ($cities as $city)
                        <option value="{{ $city }}" @selected(old('city', $customer->city) === $city)>{{ $city }}</option>
                    @endforeach
                </select>
                @error('city')<div class="error">{{ $message }}</div>@enderror
            </label>
            <label class="field">
                <span class="label">Alamat kantor</span>
                <textarea class="textarea @error('address') is-invalid @enderror" name="address" id="address" rows="2" style="min-height:72px" required>{{ old('address', $customer->address) }}</textarea>
                @error('address')<div class="error">{{ $message }}</div>@enderror
            </label>
            <div class="field">
                <span class="label">Email akun</span>
                <div class="mono small">{{ $user->email }}</div>
            </div>
        </div>
        <div class="panel-foot row between">
            <span></span>
            <button type="submit" class="btn" id="btn-save-customer">Simpan profil</button>
        </div>
    </form>
</x-layouts.portal>
