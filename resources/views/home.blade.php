<x-layouts.public title="Pesan katering makan siang untuk kantor">
    <main>
        <section class="hero">
            <div class="wrap hero-grid">
                <div>
                    <span class="eyebrow">Marketplace katering B2B</span>
                    <h1>Makan siang tim,<br>dipesan <em>sekali</em>,<br>tagihannya rapi.</h1>
                    <p class="hero-lede">
                        Kantor memilih katering, menentukan jumlah porsi dan tanggal kirim.
                        Katering menerima pesanan dan invoice yang sama, tanpa rekap manual lewat chat.
                    </p>
                    <div class="hero-cta">
                        <a href="{{ route('register') }}" class="btn btn-accent btn-lg" id="cta-register-customer">Daftar sebagai kantor</a>
                        <a href="{{ route('register', ['as' => 'merchant']) }}" class="btn btn-line btn-lg" id="cta-register-merchant">Saya pemilik katering</a>
                    </div>
                    <div class="hero-facts">
                        <div><b>{{ $stats['merchants'] }}</b><span>katering terdaftar</span></div>
                        <div><b>{{ $stats['cities'] }}</b><span>kota terlayani</span></div>
                        <div><b>H-1</b><span>batas pemesanan</span></div>
                    </div>
                </div>

                <figure class="ticket" aria-label="Contoh pesanan">
                    <img src="{{ asset('images/nasi-ayam-bakar.jpg') }}" alt="Nasi box ayam bakar dengan sambal, tempe, dan tumis kangkung" width="1200" height="896">
                    <span class="ticket-stamp">Terkirim 11:30</span>
                    <div class="ticket-body">
                        <div class="ticket-line"><span>ORD-261024-K7QPA</span><span>Jum, 24 Okt</span></div>
                        <div class="ticket-line muted"><span>PT Sinar Logistik — Lt. 12</span></div>
                        <hr style="margin:10px 0;border-top-style:dashed">
                        <div class="ticket-line"><span>Nasi Ayam Bakar × 40</span><span>Rp1.400.000</span></div>
                        <div class="ticket-line"><span>Rice Bowl Sehat × 12</span><span>Rp504.000</span></div>
                        <div class="ticket-line muted"><span>PPN 11%</span><span>Rp209.440</span></div>
                        <div class="ticket-line ticket-total"><span>Total</span><span>Rp2.113.440</span></div>
                    </div>
                </figure>
            </div>
        </section>

        <section class="section" id="cara-kerja">
            <div class="wrap">
                <div class="section-head">
                    <div>
                        <h2>Cara kerjanya</h2>
                        <p>Empat langkah dari mencari katering sampai invoice diterima kedua pihak.</p>
                    </div>
                </div>
                <div class="steps">
                    <div class="step">
                        <span class="step-n">01</span>
                        <h3>Cari katering</h3>
                        <p>Saring berdasarkan kota, jenis makanan, dan harga per porsi.</p>
                    </div>
                    <div class="step">
                        <span class="step-n">02</span>
                        <h3>Atur porsi</h3>
                        <p>Pilih beberapa menu sekaligus, isi jumlah porsi, tanggal dan jam kirim.</p>
                    </div>
                    <div class="step">
                        <span class="step-n">03</span>
                        <h3>Invoice terbit</h3>
                        <p>Nomor invoice dibuat otomatis lengkap dengan PPN, bisa diunduh PDF.</p>
                    </div>
                    <div class="step">
                        <span class="step-n">04</span>
                        <h3>Katering kirim</h3>
                        <p>Katering mengonfirmasi, mengirim, lalu menandai invoice lunas.</p>
                    </div>
                </div>
            </div>
        </section>

        @if ($merchants->isNotEmpty())
            <section class="section">
                <div class="wrap">
                    <div class="section-head">
                        <div>
                            <h2>Katering di platform</h2>
                            <p>Sebagian katering yang siap menerima pesanan kantor.</p>
                        </div>
                        <a href="{{ route('login') }}" class="btn btn-line">Lihat semua</a>
                    </div>
                    <div class="caterer-list">
                        @foreach ($merchants as $merchant)
                            @php $cover = $merchant->menus->first(); @endphp
                            <a href="{{ auth()->user()?->isCustomer() ? route('customer.caterings.show', $merchant) : route('login') }}" class="caterer">
                                <div class="caterer-photo">
                                    @if ($cover)<img src="{{ $cover->photoUrl() }}" alt="{{ $cover->name }}" loading="lazy">@endif
                                </div>
                                <div>
                                    <h3 class="caterer-name">{{ $merchant->company_name }}</h3>
                                    <div class="caterer-meta">
                                        <span>{{ $merchant->city }}</span>
                                        <span>{{ $merchant->menus_count }} menu</span>
                                    </div>
                                    <p class="caterer-desc">{{ $merchant->description }}</p>
                                </div>
                                <div class="caterer-price">
                                    <span class="muted xs">mulai</span>
                                    <b>{{ rupiah($merchant->menus_min_price) }}</b>
                                    <span class="muted xs">per porsi</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        <section class="section">
            <div class="wrap">
                <div class="split">
                    <div>
                        <span class="upper muted">Untuk kantor</span>
                        <h3 style="margin-top:8px">Satu tempat untuk semua pesanan makan siang.</h3>
                        <ul>
                            <li>Bandingkan katering per kota dan jenis makanan</li>
                            <li>Pesan banyak menu dalam satu pesanan</li>
                            <li>Riwayat pesanan dan invoice PDF untuk finance</li>
                        </ul>
                        <div style="display:flex;gap:12px;align-items:center;margin-top:16px">
                            <a href="{{ route('register') }}" class="btn">Daftar kantor</a>
                            <a href="{{ route('login') }}" class="small" style="font-weight:600">Masuk Portal Kantor &rarr;</a>
                        </div>
                    </div>
                    <div>
                        <span class="upper" style="opacity:.6">Untuk katering</span>
                        <h3 style="margin-top:8px">Terima pesanan korporat tanpa rekap manual.</h3>
                        <ul>
                            <li>Kelola profil, menu, foto, dan harga sendiri</li>
                            <li>Daftar pesanan dengan filter tanggal kirim</li>
                            <li>Invoice otomatis, tandai lunas saat dibayar</li>
                        </ul>
                        <div style="display:flex;gap:12px;align-items:center;margin-top:16px">
                            <a href="{{ route('register', ['as' => 'merchant']) }}" class="btn btn-line">Daftarkan katering</a>
                            <a href="{{ route('merchant.login') }}" class="small" style="font-weight:600">Masuk Portal Katering &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.public>
