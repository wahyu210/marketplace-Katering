<x-layouts.public title="Daftar" :footer="false">
    @php $selected = old('role', $role->value); @endphp
    <main class="auth">
        <div class="auth-aside">
            <img src="{{ asset('images/rice-bowl.jpg') }}" alt="">
            <div class="auth-aside-copy">
                <blockquote>Kantor memesan, katering mengirim, invoice tercatat di kedua sisi.</blockquote>
                <cite>Gratis untuk mendaftar</cite>
            </div>
        </div>
        <div class="auth-form">
            <div>
                <h1>Buat akun</h1>
                <p class="muted" style="margin:0 0 24px">Pilih jenis akun, lalu isi data perusahaan.</p>

                <x-flash inline />

                <form method="POST" action="{{ route('register') }}" novalidate id="register-form">
                    @csrf
                    <div class="field">
                        <span class="label">Saya mendaftar sebagai</span>
                        <div class="segmented" role="radiogroup">
                            <input type="radio" name="role" id="role-customer" value="customer" @checked($selected === 'customer')>
                            <label for="role-customer">Kantor</label>
                            <input type="radio" name="role" id="role-merchant" value="merchant" @checked($selected === 'merchant')>
                            <label for="role-merchant">Katering</label>
                        </div>
                    </div>

                    <label class="field">
                        <span class="label" data-company-label>{{ $selected === 'merchant' ? 'Nama katering' : 'Nama kantor / perusahaan' }}</span>
                        <input class="input @error('company_name') is-invalid @enderror" name="company_name" id="company_name" value="{{ old('company_name') }}" required>
                        @error('company_name')<div class="error">{{ $message }}</div>@enderror
                    </label>

                    <div class="grid-2 field">
                        <label class="field">
                            <span class="label">Nama penanggung jawab</span>
                            <input class="input @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" required autocomplete="name">
                            @error('name')<div class="error">{{ $message }}</div>@enderror
                        </label>
                        <label class="field">
                            <span class="label">No. telepon / WhatsApp</span>
                            <input class="input @error('phone') is-invalid @enderror" name="phone" id="phone" value="{{ old('phone') }}" required inputmode="tel" placeholder="0812 3456 7890">
                            @error('phone')<div class="error">{{ $message }}</div>@enderror
                        </label>
                    </div>

                    <label class="field">
                        <span class="label">Kota</span>
                        <select class="select @error('city') is-invalid @enderror" name="city" id="city" required>
                            <option value="">Pilih kota</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city }}" @selected(old('city') === $city)>{{ $city }}</option>
                            @endforeach
                        </select>
                        @error('city')<div class="error">{{ $message }}</div>@enderror
                    </label>

                    <label class="field">
                        <span class="label">Alamat lengkap</span>
                        <textarea class="textarea @error('address') is-invalid @enderror" name="address" id="address" rows="2" required style="min-height:72px">{{ old('address') }}</textarea>
                        @error('address')<div class="error">{{ $message }}</div>@enderror
                    </label>

                    <label class="field">
                        <span class="label">Email</span>
                        <input class="input @error('email') is-invalid @enderror" type="email" name="email" id="reg-email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')<div class="error">{{ $message }}</div>@enderror
                    </label>

                    <div class="grid-2 field">
                        <label class="field">
                            <span class="label">Password</span>
                            <input class="input @error('password') is-invalid @enderror" type="password" name="password" id="reg-password" required autocomplete="new-password">
                            <div class="hint">Minimal 8 karakter.</div>
                            @error('password')<div class="error">{{ $message }}</div>@enderror
                        </label>
                        <label class="field">
                            <span class="label">Ulangi password</span>
                            <input class="input" type="password" name="password_confirmation" id="reg-password-confirm" required autocomplete="new-password">
                        </label>
                    </div>

                    <div class="field">
                        <button type="submit" class="btn btn-accent btn-block btn-lg" id="btn-register">Buat akun</button>
                    </div>
                </form>

                <p class="small" style="margin-top:20px">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
            </div>
        </div>
    </main>

    <script>
        document.querySelectorAll('input[name=role]').forEach((r) => r.addEventListener('change', () => {
            document.querySelector('[data-company-label]').textContent =
                r.value === 'merchant' ? 'Nama katering' : 'Nama kantor / perusahaan';
        }));
    </script>
</x-layouts.public>
