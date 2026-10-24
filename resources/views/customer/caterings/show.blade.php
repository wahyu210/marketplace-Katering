<x-layouts.portal :title="$merchant->company_name" :description="Str::limit($merchant->description, 150)">
    <header class="page-head">
        <div class="row" style="--gap:18px;align-items:flex-end">
            <span class="monogram monogram-lg">
                @if ($merchant->logoUrl())<img src="{{ $merchant->logoUrl() }}" alt="">@else{{ $merchant->initials() }}@endif
            </span>
            <div>
                <a href="{{ route('customer.caterings.index') }}" class="crumb">← Cari katering</a>
                <h1>{{ $merchant->company_name }}</h1>
                <p>{{ $merchant->city }} · <span class="mono">{{ $merchant->phone }}</span></p>
            </div>
        </div>
    </header>

    @if ($merchant->description)
        <p style="max-width:720px;margin:-8px 0 32px;font-size:16px;color:var(--ink-2)">{{ $merchant->description }}</p>
    @endif

    <form method="POST" action="{{ route('customer.orders.store', $merchant) }}" novalidate data-order-form data-tax-rate="{{ $taxRate }}" id="order-form">
        @csrf
        <div class="order-layout">
            <div>
                @error('items')<div class="flash is-error" style="margin-bottom:16px">{{ $message }}</div>@enderror

                @foreach ($menusByCategory as $category => $menus)
                    <section class="menu-section">
                        <h2>{{ $category }}</h2>
                        @foreach ($menus as $menu)
                            <div class="menu-row" data-menu data-price="{{ $menu->price }}" data-min="{{ $menu->min_order }}" data-name="{{ $menu->name }}">
                                <div class="menu-row-photo"><img src="{{ $menu->photoUrl() }}" alt="{{ $menu->name }}" loading="lazy"></div>
                                <div>
                                    <h3>{{ $menu->name }}</h3>
                                    <p>{{ $menu->description }}</p>
                                    <span class="price">{{ rupiah($menu->price) }}</span>
                                    <span class="muted xs"> / porsi · min. {{ $menu->min_order }} porsi</span>
                                    <div class="error" data-min-warn hidden>Minimal {{ $menu->min_order }} porsi.</div>
                                    @error('items.'.$menu->id)<div class="error">{{ $message }}</div>@enderror
                                </div>
                                <div class="stepper-cell">
                                    <label class="sr-only" for="qty-{{ $menu->id }}">Jumlah porsi {{ $menu->name }}</label>
                                    <div class="stepper">
                                        <button type="button" data-step="-1" aria-label="Kurangi">−</button>
                                        <input type="number" min="0" inputmode="numeric" name="items[{{ $menu->id }}]" id="qty-{{ $menu->id }}" value="{{ old('items.'.$menu->id, 0) }}">
                                        <button type="button" data-step="1" aria-label="Tambah">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endforeach
            </div>

            <aside class="summary panel">
                <div class="panel-head"><h2>Pesanan Anda</h2><span class="small muted"><span class="mono" data-portions>0</span> porsi</span></div>
                <div class="summary-lines" data-summary-lines></div>

                <div style="padding:16px 24px;border-top:1px solid var(--rule)">
                    <div class="grid-2" style="gap:12px">
                        <label class="field">
                            <span class="label">Tanggal kirim</span>
                            <input class="input @error('delivery_date') is-invalid @enderror" type="date" name="delivery_date" id="delivery_date" min="{{ $minDate }}" value="{{ old('delivery_date', $minDate) }}" required>
                        </label>
                        <label class="field">
                            <span class="label">Jam tiba</span>
                            <select class="select @error('delivery_time') is-invalid @enderror" name="delivery_time" id="delivery_time" required>
                                @foreach ($slots as $slot)
                                    <option value="{{ $slot }}" @selected(old('delivery_time', '11:30') === $slot)>{{ $slot }}</option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                    @error('delivery_date')<div class="error">{{ $message }}</div>@enderror
                    @error('delivery_time')<div class="error">{{ $message }}</div>@enderror
                    <label class="field">
                        <span class="label">Alamat pengiriman</span>
                        <textarea class="textarea @error('delivery_address') is-invalid @enderror" name="delivery_address" id="delivery_address" rows="2" style="min-height:64px" required>{{ old('delivery_address', $customer->office_name.', '.$customer->address) }}</textarea>
                        @error('delivery_address')<div class="error">{{ $message }}</div>@enderror
                    </label>
                    <label class="field">
                        <span class="label">Catatan <span class="opt">(opsional)</span></span>
                        <input class="input" name="notes" id="notes" value="{{ old('notes') }}" placeholder="Mis. 5 porsi tanpa sambal, titip di resepsionis">
                    </label>
                </div>

                <div class="summary-total">
                    <div class="row between small"><span class="muted">Subtotal</span><span data-subtotal>Rp0</span></div>
                    <div class="row between small"><span class="muted">PPN {{ round($taxRate * 100) }}%</span><span data-tax>Rp0</span></div>
                    <div class="row between grand"><span>Total</span><span data-total>Rp0</span></div>
                </div>
                <div style="padding:0 24px 24px">
                    <button type="submit" class="btn btn-accent btn-block btn-lg" data-submit id="btn-place-order">Buat pesanan & invoice</button>
                    <p class="muted xs" style="margin:10px 0 0;text-align:center">Pembayaran mengikuti invoice, dikonfirmasi oleh katering.</p>
                </div>
            </aside>
        </div>
    </form>
</x-layouts.portal>
