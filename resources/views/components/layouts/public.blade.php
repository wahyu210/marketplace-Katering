@props(['title' => null, 'description' => null, 'footer' => true])
<x-layouts.base :title="$title" :description="$description ?? 'Marketplace katering untuk kantor: cari katering, pesan makan siang tim, dan kelola invoice dalam satu tempat.'">
    <header class="site-head">
        <div class="wrap">
            <x-brand />
            <nav class="site-nav" aria-label="Utama">
                @auth
                    <a href="{{ route(auth()->user()->homeRoute()) }}" class="btn btn-sm" id="nav-portal">Buka portal</a>
                @else
                    <a href="{{ route('home') }}#cara-kerja" class="hide-sm">Cara kerja</a>
                    <a href="{{ route('merchant.login') }}" class="hide-sm" id="nav-login-merchant">Portal Katering</a>
                    <a href="{{ route('login') }}" id="nav-login">Masuk Kantor</a>
                    <a href="{{ route('register') }}" class="btn btn-sm" id="nav-register">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    {{ $slot }}

    @if ($footer)
        <footer class="site-foot">
            <div class="wrap">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}. Makan siang kantor, tanpa repot.</span>
                <span class="mono">Dibuat dengan Laravel {{ app()->version() }}</span>
            </div>
        </footer>
    @endif
</x-layouts.base>
