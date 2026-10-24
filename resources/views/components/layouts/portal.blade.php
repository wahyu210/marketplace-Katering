@props(['title', 'description' => null])
@php
    $user = auth()->user();
    $isMerchant = $user->isMerchant();
    $profile = $isMerchant ? $user->merchant : $user->customer;
    $orgName = $isMerchant ? $profile->company_name : $profile->office_name;

    $pendingCount = $isMerchant
        ? $profile->orders()->where('status', \App\Enums\OrderStatus::Pending)->count()
        : 0;

    $nav = $isMerchant
        ? [
            ['Ringkasan', 'merchant.dashboard', 'merchant.dashboard', null],
            ['Pesanan & invoice', 'merchant.orders.index', 'merchant.orders.*', $pendingCount ?: null],
            ['Menu', 'merchant.menus.index', 'merchant.menus.*', null],
            ['Profil katering', 'merchant.profile.edit', 'merchant.profile.*', null],
        ]
        : [
            ['Cari katering', 'customer.caterings.index', 'customer.caterings.*', null],
            ['Pesanan & invoice', 'customer.orders.index', 'customer.orders.*', null],
            ['Profil kantor', 'customer.profile.edit', 'customer.profile.*', null],
        ];
@endphp
<x-layouts.base :title="$title" :description="$description">
    <div class="mobile-bar">
        <x-brand />
        <button type="button" class="btn btn-line btn-sm" data-side-toggle aria-label="Buka menu">Menu</button>
    </div>

    <div class="portal">
        <aside class="side" aria-label="Navigasi portal">
            <div class="side-brand"><x-brand /></div>
            <div class="side-who">
                @if ($isMerchant)
                    <span class="monogram">
                        @if ($profile->logoUrl())<img src="{{ $profile->logoUrl() }}" alt="">@else{{ $profile->initials() }}@endif
                    </span>
                @endif
                <div class="grow">
                    <b>{{ $orgName }}</b>
                    <span>{{ $isMerchant ? 'Portal katering' : 'Portal kantor' }} · {{ $profile->city }}</span>
                </div>
            </div>
            <nav class="side-nav">
                @foreach ($nav as [$label, $route, $pattern, $count])
                    <a href="{{ route($route) }}" @class(['is-active' => request()->routeIs($pattern)]) id="nav-{{ Str::slug($label) }}">
                        <span>{{ $label }}</span>
                        @if ($count)<span class="count" title="Menunggu konfirmasi">{{ $count }}</span>@endif
                    </a>
                @endforeach
            </nav>
            <div class="side-foot">
                <div class="small" style="margin-bottom:10px">
                    <div>{{ $user->name }}</div>
                    <div class="muted xs">{{ $user->email }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-line btn-sm btn-block" id="btn-logout">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="main">
            <div class="main-inner">
                <x-flash />
                {{ $slot }}
            </div>
        </main>
    </div>
</x-layouts.base>
