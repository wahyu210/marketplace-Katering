<x-layouts.public title="Masuk Portal Katering" :footer="false">
    <main class="auth">
        <div class="auth-aside">
            <img src="{{ asset('images/rendang.jpg') }}" alt="Katering Nusantara">
            <div class="auth-aside-copy">
                <blockquote>"Dulu rekap pesanan 60 orang lewat grup chat. Sekarang tinggal cek daftar pesanan pagi hari dan langsung masak."</blockquote>
                <cite>— Dapur Nusantara, Katering Mitra</cite>
            </div>
        </div>
        <div class="auth-form">
            <div>
                <div class="badge badge-info" style="margin-bottom:12px;display:inline-block">Portal Katering (Merchant)</div>
                <h1>Masuk Akun Katering</h1>
                <p class="muted" style="margin:0 0 28px">Kelola menu makanan, terima pesanan kantor, dan pantau invoice masuk.</p>

                <x-flash inline />

                <form method="POST" action="{{ route('merchant.login') }}" novalidate>
                    @csrf
                    <label class="field">
                        <span class="label">Email Katering / Pemilik</span>
                        <input class="input @error('email') is-invalid @enderror" type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="contoh: dapur@katering.com">
                        @error('email')<div class="error">{{ $message }}</div>@enderror
                    </label>
                    <label class="field">
                        <span class="label">Password</span>
                        <input class=
                        "input" type="password" name="password" id="password" required autocomplete="current-password" placeholder="••••••••">
                    </label>
                    <div class="field row between">
                        <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    </div>
                    <div class="field">
                        <button type="submit" class="btn btn-block btn-lg" id="btn-login-merchant">Masuk ke Portal Katering</button>
                    </div>
                </form>

                <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e5e5;display:flex;flex-direction:column;gap:10px">
                    <p class="small" style="margin:0">
                        Belum mendaftarkan katering Anda?
                        <a href="{{ route('register', ['as' => 'merchant']) }}" style="font-weight:600">Daftarkan Katering Sekarang</a>
                    </p>
                    <p class="small muted" style="margin:0">
                        Anda perwakilan kantor?
                        <a href="{{ route('login') }}" style="font-weight:600;color:var(--brand)">Masuk ke Portal Kantor &rarr;</a>
                    </p>
                </div>

                @if (app()->isLocal())
                    <div class="demo-box" style="margin-top:24px">
                        <div class="upper muted" style="margin-bottom:6px">Akun Demo Katering · password <code>password</code></div>
                        <div>Dapur Nusantara: <code>merchant@demo.test</code></div>
                        <div>Sehat Catering: <code>merchant2@demo.test</code></div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</x-layouts.public>
