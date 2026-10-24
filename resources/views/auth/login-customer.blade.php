<x-layouts.public title="Masuk Portal Kantor" :footer="false">
    <main class="auth">
        <div class="auth-aside">
            <img src="{{ asset('images/rice-bowl.jpg') }}" alt="Pilihan makan siang kantor">
            <div class="auth-aside-copy">
                <blockquote>"Pesan makan siang untuk seluruh tim kantor, pilih menu variatif dari katering terpercaya, dan kelola invoice dalam satu tempat."</blockquote>
                <cite>— Portal Kantor & Perusahaan</cite>
            </div>
        </div>
        <div class="auth-form">
            <div>
                <div class="badge badge-accent" style="margin-bottom:12px;display:inline-block">Portal Kantor</div>
                <h1>Masuk Akun Kantor</h1>
                <p class="muted" style="margin:0 0 28px">Pesan katering untuk makan siang tim dan pantau tagihan.</p>

                <x-flash inline />

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <label class="field">
                        <span class="label">Email Kantor / PIC</span>
                        <input class="input @error('email') is-invalid @enderror" type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="contoh: hrd@perusahaan.com">
                        @error('email')<div class="error">{{ $message }}</div>@enderror
                    </label>
                    <label class="field">
                        <span class="label">Password</span>
                        <input class="input" type="password" name="password" id="password" required autocomplete="current-password" placeholder="••••••••">
                    </label>
                    <div class="field row between">
                        <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya</label>
                    </div>
                    <div class="field">
                        <button type="submit" class="btn btn-accent btn-block btn-lg" id="btn-login-customer">Masuk ke Portal Kantor</button>
                    </div>
                </form>

                <div style="margin-top:24px;padding-top:16px;border-top:1px solid #e5e5e5;display:flex;flex-direction:column;gap:10px">
                    <p class="small" style="margin:0">
                        Belum punya akun kantor?
                        <a href="{{ route('register') }}" style="font-weight:600">Daftar Akun Kantor Sekarang</a>
                    </p>
                    <p class="small muted" style="margin:0">
                        Anda pemilik katering?
                        <a href="{{ route('merchant.login') }}" style="font-weight:600;color:var(--brand)">Masuk ke Portal Katering &rarr;</a>
                    </p>
                </div>

                @if (app()->isLocal())
                    <div class="demo-box" style="margin-top:24px">
                        <div class="upper muted" style="margin-bottom:6px">Akun Demo Kantor · password <code>password</code></div>
                        <div>PT Maju Bersama: <code>customer@demo.test</code></div>
                        <div>Startup Digital: <code>customer2@demo.test</code></div>
                    </div>
                @endif
            </div>
        </div>
    </main>
</x-layouts.public>
