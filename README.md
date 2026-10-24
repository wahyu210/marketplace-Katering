# Marketplace Katering — B2B Katering Kantor
> Aplikasi berbasis web dengan PHP Laravel untuk menghubungkan perusahaan katering makanan dengan kantor yang membutuhkan konsumsi makan siang karyawan.


---

## Fitur Utama

### 1. Portal Katering (Merchant)
- **Registrasi & Login**: Registrasi khusus katering dengan data PIC, nama usaha, kota, alamat, dan deskripsi.
- **Pengelolaan Profil**: Kelola profil perusahaan katering dan upload logo.
- **Pengelolaan Menu (CRUD)**: Tambah, edit, dan hapus menu makanan dengan upload foto, harga, deskripsi, batas minimal order, dan status ketersediaan.
- **Daftar Pesanan & Invoice**: Memantau daftar pesanan masuk dengan filter status dan tanggal kirim.
- **Status Lifecycle**: Memperbarui status pesanan bertahap (*Menunggu Konfirmasi &rarr; Dikonfirmasi &rarr; Sedang Dimasak &rarr; Sedang Dikirim &rarr; Terkirim / Dibatalkan*).
- **Manajemen Invoice**: Melihat invoice pesanan dan menandai status pelunasan (*Mark as Paid*).

### 2. Portal Kantor (Customer)
- **Registrasi & Login**: Registrasi akun kantor/perusahaan.
- **Pencarian Katering**: Cari katering berdasarkan kota, jenis makanan/kategori, dan kata kunci.
- **Pemesanan Katering**: Pemilihan menu dan jumlah porsi (dengan validasi otomatis batas minimal order per menu), tanggal pengiriman (buffer H-1), slot jam antar, alamat kantor, dan catatan khusus.
- **Riwayat Pesanan & Timeline**: Melacak progress status pesanan melalui indikator visual timeline interaktif.
- **Invoice & Cetak PDF**: Rincian subtotal, perhitungan PPN 11%, status pembayaran, dan fitur download invoice resmi format PDF.
- **Pembatalan Pesanan**: Membatalkan pesanan yang belum diproses oleh pihak katering.

---

## Tech Stack & Arsitektur
- **Backend**: Laravel 13 (PHP 8.4)
- **Database**: PostgreSQL 17 (Docker)
- **Frontend**: Laravel Blade Components + Custom Vanilla CSS (clean, responsive, no bloated framework)
- **PDF Engine**: `barryvdh/laravel-dompdf` (Invoice A4 PDF)
- **Testing**: PHPUnit / Laravel Test Suite (20 tests, 92 assertions pass 100%)
- **Environment**: Docker & Docker Compose

---

## Cara Menjalankan Aplikasi

### 1. Menjalankan Docker
```bash
docker compose up -d
```
Aplikasi dapat diakses di browser: **[http://localhost:8000](http://localhost:8000)**

### 2. Menjalankan Migrasi & Database Seeder (Demo Data)
```bash
docker exec katering-app php artisan migrate:fresh --seed --force
```

### 3. Menjalankan Automated Test
```bash
docker exec katering-app php artisan test
```

---

## Akun Demo untuk Uji Coba

Semua akun menggunakan password: `password`

| Role / Tipe Akun | Halaman Login | Entitas / Kantor | Email | Password |
|---|---|---|---|---|
| **Katering (Merchant 1)** | [`/merchant/login`](http://localhost:8000/merchant/login) | Dapur Nusantara (Jaksel) | `merchant@demo.test` | `password` |
| **Katering (Merchant 2)** | [`/merchant/login`](http://localhost:8000/merchant/login) | Sehat Catering (Jakpus) | `merchant2@demo.test` | `password` |
| **Kantor (Customer 1)** | [`/login`](http://localhost:8000/login) | PT Maju Bersama (Jaksel) | `customer@demo.test` | `password` |
| **Kantor (Customer 2)** | [`/login`](http://localhost:8000/login) | Startup Digital (Jakpus) | `customer2@demo.test` | `password` |

---

## Hasil Pengujian (Automated Tests)
```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AuthTest
  ✓ registration screen can be rendered
  ✓ merchant can register
  ✓ customer can register
  ✓ user can login and redirect to appropriate portal
  ✓ user can logout

   PASS  Tests\Feature\CustomerPortalTest
  ✓ customer can search and filter caterings
  ✓ customer can view catering detail
  ✓ customer can place order and invoice is generated
  ✓ customer cannot place order below min order
  ✓ customer can view orders and invoices
  ✓ customer can cancel order
  ✓ customer can update profile

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

   PASS  Tests\Feature\MerchantPortalTest
  ✓ merchant can view dashboard
  ✓ merchant can update profile
  ✓ merchant can create menu
  ✓ merchant can update menu
  ✓ merchant can delete menu
  ✓ merchant can view orders and update status

  Tests:    20 passed (92 assertions)
```
