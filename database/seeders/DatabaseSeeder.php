<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Menu;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ──────────────────────────────
        $categories = collect([
            'Nasi kotak' => 'nasi-kotak',
            'Prasmanan' => 'prasmanan',
            'Diet & sehat' => 'diet-sehat',
            'Western' => 'western',
            'Snack box' => 'snack-box',
        ])->map(fn ($slug, $name) => Category::create(['name' => $name, 'slug' => $slug]));

        // ── Copy seed images ─────────────────────────
        $imageDir = database_path('seeders/images');
        $imageMap = [];
        $files = [
            'nasi-ayam-bakar.jpg',
            'rendang.jpg',
            'rice-bowl.jpg',
            'prasmanan.jpg',
            'snack-box.jpg',
            'salad-bowl.jpg',
            'nasi-liwet.jpg',
            'chicken-steak.jpg',
        ];
        foreach ($files as $file) {
            $source = $imageDir.'/'.$file;
            if (File::exists($source)) {
                $dest = 'menus/seed-'.$file;
                Storage::disk('public')->put($dest, File::get($source));
                $imageMap[$file] = $dest;
            }
        }
        $defaultPhoto = $imageMap['nasi-ayam-bakar.jpg'] ?? 'menus/placeholder.jpg';

        // ── Merchant 1: Dapur Nusantara ──────────────
        $m1user = User::create([
            'name' => 'Budi Santoso',
            'email' => 'merchant@demo.test',
            'password' => 'password',
            'role' => Role::Merchant,
        ]);
        $m1 = Merchant::create([
            'user_id' => $m1user->id,
            'company_name' => 'Dapur Nusantara',
            'slug' => 'dapur-nusantara',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Kemang Raya No. 45, Bangka, Mampang Prapatan',
            'phone' => '021-7198234',
            'description' => 'Katering masakan Indonesia autentik untuk kantor. Bahan segar setiap hari, bumbu racikan sendiri. Melayani pesanan mulai 10 porsi. Pengalaman 8 tahun melayani kantoran di Jabodetabek.',
        ]);

        $m1Menus = [
            ['name' => 'Nasi Ayam Bakar Madu', 'category' => 'Nasi kotak', 'price' => 28000, 'min_order' => 10, 'photo' => $imageMap['nasi-ayam-bakar.jpg'] ?? $defaultPhoto, 'description' => 'Nasi putih pulen, ayam bakar madu yang empuk, lalapan segar, sambal terasi, dan kerupuk udang. Dikemas dalam kotak kraft ramah lingkungan.'],
            ['name' => 'Nasi Rendang Padang', 'category' => 'Nasi kotak', 'price' => 32000, 'min_order' => 10, 'photo' => $imageMap['rendang.jpg'] ?? $defaultPhoto, 'description' => 'Rendang sapi empuk masak kering dengan bumbu rempah khas Minang, nasi putih, sayur daun singkong, dan sambal hijau.'],
            ['name' => 'Rice Bowl Ayam Teriyaki', 'category' => 'Diet & sehat', 'price' => 25000, 'min_order' => 15, 'photo' => $imageMap['rice-bowl.jpg'] ?? $defaultPhoto, 'description' => 'Nasi hangat dengan topping ayam fillet saus teriyaki, edamame, wortel, dan taburan wijen. Porsi pas untuk makan siang.'],
            ['name' => 'Paket Prasmanan Standar', 'category' => 'Prasmanan', 'price' => 45000, 'min_order' => 30, 'photo' => $imageMap['prasmanan.jpg'] ?? $defaultPhoto, 'description' => '4 lauk (ayam, daging, ikan, tahu/tempe), 2 sayur, nasi putih, buah potong, dan air mineral. Termasuk peralatan prasmanan dan penyajian.'],
            ['name' => 'Snack Box Premium', 'category' => 'Snack box', 'price' => 22000, 'min_order' => 20, 'photo' => $imageMap['snack-box.jpg'] ?? $defaultPhoto, 'description' => '3 jenis snack tradisional (risol mayo, pastel gurih, dan kue lapis manis), plus satu buah pisang segar dan air mineral kemasan.'],
        ];

        foreach ($m1Menus as $menuData) {
            $cat = $categories[$menuData['category']];
            Menu::create([
                'merchant_id' => $m1->id,
                'category_id' => $cat->id,
                'name' => $menuData['name'],
                'description' => $menuData['description'],
                'photo' => $menuData['photo'],
                'price' => $menuData['price'],
                'min_order' => $menuData['min_order'],
                'is_available' => true,
            ]);
        }

        // ── Merchant 2: Sehat Catering ────────────────
        $m2user = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'merchant2@demo.test',
            'password' => 'password',
            'role' => Role::Merchant,
        ]);
        $m2 = Merchant::create([
            'user_id' => $m2user->id,
            'company_name' => 'Sehat Catering',
            'slug' => 'sehat-catering',
            'city' => 'Jakarta Pusat',
            'address' => 'Jl. Cikini Raya No. 12, Menteng',
            'phone' => '021-3145678',
            'description' => 'Katering sehat untuk karyawan aktif. Menu rendah kalori, tinggi protein, tanpa MSG. Paket diet custom tersedia.',
        ]);

        $m2Menus = [
            ['name' => 'Salad Bowl Protein', 'category' => 'Diet & sehat', 'price' => 30000, 'min_order' => 10, 'photo' => $imageMap['salad-bowl.jpg'] ?? $defaultPhoto, 'description' => 'Mix sayuran segar dengan dada ayam panggang, telur rebus, edamame, tomat ceri, dan dressing wijen. Tanpa MSG, rendah kalori.'],
            ['name' => 'Nasi Liwet Solo', 'category' => 'Nasi kotak', 'price' => 27000, 'min_order' => 15, 'photo' => $imageMap['nasi-liwet.jpg'] ?? $defaultPhoto, 'description' => 'Nasi liwet gurih masak santan, ayam suwir, teri kacang, telur pindang, dan lalapan. Rasa kampung autentik di tengah kota.'],
            ['name' => 'Chicken Steak & Mashed Potato', 'category' => 'Western', 'price' => 35000, 'min_order' => 10, 'photo' => $imageMap['chicken-steak.jpg'] ?? $defaultPhoto, 'description' => 'Dada ayam panggang juicy dengan saus mushroom, mashed potato lembut, dan salad sayuran. Cocok untuk acara kantor.'],
        ];

        foreach ($m2Menus as $menuData) {
            $cat = $categories[$menuData['category']];
            Menu::create([
                'merchant_id' => $m2->id,
                'category_id' => $cat->id,
                'name' => $menuData['name'],
                'description' => $menuData['description'],
                'photo' => $menuData['photo'],
                'price' => $menuData['price'],
                'min_order' => $menuData['min_order'],
                'is_available' => true,
            ]);
        }

        // ── Customer 1: PT Maju Bersama ──────────────
        $c1user = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'customer@demo.test',
            'password' => 'password',
            'role' => Role::Customer,
        ]);
        $c1 = Customer::create([
            'user_id' => $c1user->id,
            'office_name' => 'PT Maju Bersama',
            'city' => 'Jakarta Selatan',
            'address' => 'Gedung Graha Mandiri Lt. 5, Jl. Sudirman Kav. 54, Senayan',
            'phone' => '021-5790123',
        ]);

        // ── Customer 2: Startup Digital Nusantara ─────
        $c2user = User::create([
            'name' => 'Dewi Kartika',
            'email' => 'customer2@demo.test',
            'password' => 'password',
            'role' => Role::Customer,
        ]);
        $c2 = Customer::create([
            'user_id' => $c2user->id,
            'office_name' => 'Startup Digital Nusantara',
            'city' => 'Jakarta Pusat',
            'address' => 'WeWork Revenue Tower Lt. 12, Jl. Jend. Sudirman',
            'phone' => '021-2567890',
        ]);

        // ── Demo Orders ──────────────────────────────
        $m1menus = $m1->menus()->get();
        $m2menus = $m2->menus()->get();

        // Order 1: Customer 1 → Merchant 1 — Confirmed
        $this->createOrder(
            customer: $c1,
            merchant: $m1,
            menus: [
                [$m1menus[0], 20], // Nasi Ayam Bakar x20
                [$m1menus[1], 15], // Nasi Rendang x15
            ],
            deliveryDate: today()->addDays(2),
            deliveryTime: '12:00',
            deliveryAddress: $c1->address,
            status: OrderStatus::Confirmed,
            invoiceStatus: InvoiceStatus::Unpaid,
        );

        // Order 2: Customer 2 → Merchant 1 — Pending
        $this->createOrder(
            customer: $c2,
            merchant: $m1,
            menus: [
                [$m1menus[2], 25], // Rice Bowl x25
                [$m1menus[4], 25], // Snack Box x25
            ],
            deliveryDate: today()->addDays(3),
            deliveryTime: '11:30',
            deliveryAddress: $c2->address,
            status: OrderStatus::Pending,
            invoiceStatus: InvoiceStatus::Unpaid,
        );

        // Order 3: Customer 1 → Merchant 2 — Delivered & Paid
        $this->createOrder(
            customer: $c1,
            merchant: $m2,
            menus: [
                [$m2menus[0], 15], // Salad Bowl x15
                [$m2menus[2], 10], // Chicken Steak x10
            ],
            deliveryDate: today()->subDays(3),
            deliveryTime: '12:00',
            deliveryAddress: $c1->address,
            status: OrderStatus::Delivered,
            invoiceStatus: InvoiceStatus::Paid,
            issuedDaysAgo: 5,
        );

        // Order 4: Customer 2 → Merchant 2 — Cancelled
        $this->createOrder(
            customer: $c2,
            merchant: $m2,
            menus: [
                [$m2menus[1], 20], // Nasi Liwet x20
            ],
            deliveryDate: today()->addDays(1),
            deliveryTime: '11:00',
            deliveryAddress: $c2->address,
            status: OrderStatus::Cancelled,
            invoiceStatus: InvoiceStatus::Void,
        );

        $this->command->info('');
        $this->command->info('  Demo accounts seeded:');
        $this->command->info('  ┌──────────────────────────┬───────────────────────┬────────────┐');
        $this->command->info('  │ Role                     │ Email                 │ Password   │');
        $this->command->info('  ├──────────────────────────┼───────────────────────┼────────────┤');
        $this->command->info('  │ Katering (Dapur Nusantra)│ merchant@demo.test    │ password   │');
        $this->command->info('  │ Katering (Sehat Catering)│ merchant2@demo.test   │ password   │');
        $this->command->info('  │ Kantor (PT Maju Bersama) │ customer@demo.test    │ password   │');
        $this->command->info('  │ Kantor (Startup Digital) │ customer2@demo.test   │ password   │');
        $this->command->info('  └──────────────────────────┴───────────────────────┴────────────┘');
        $this->command->info('');
    }

    private function createOrder(
        Customer $customer,
        Merchant $merchant,
        array $menus,
        $deliveryDate,
        string $deliveryTime,
        string $deliveryAddress,
        OrderStatus $status,
        InvoiceStatus $invoiceStatus,
        int $issuedDaysAgo = 0,
    ): void {
        $lines = collect($menus)->map(fn ($item) => [
            'menu_id' => $item[0]->id,
            'menu_name' => $item[0]->name,
            'unit_price' => $item[0]->price,
            'quantity' => $item[1],
            'subtotal' => $item[0]->price * $item[1],
        ]);

        $subtotal = (int) $lines->sum('subtotal');
        $orderNumber = 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(5));

        $order = Order::create([
            'order_number' => $orderNumber,
            'customer_id' => $customer->id,
            'merchant_id' => $merchant->id,
            'delivery_date' => $deliveryDate,
            'delivery_time' => $deliveryTime,
            'delivery_address' => $deliveryAddress,
            'notes' => null,
            'status' => $status,
            'subtotal' => $subtotal,
        ]);

        $order->items()->createMany($lines->all());

        $tax = (int) round($subtotal * config('katering.tax_rate'));
        $issuedAt = now()->subDays($issuedDaysAgo);

        $invPrefix = 'INV/'.$issuedAt->format('Ym').'/';
        $lastInv = Invoice::where('invoice_number', 'like', $invPrefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');
        $seq = $lastInv ? ((int) Str::afterLast($lastInv, '/')) + 1 : 1;
        $invoiceNumber = $invPrefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);

        $paidAt = $invoiceStatus === InvoiceStatus::Paid ? $issuedAt->copy()->addDays(2) : null;

        $order->invoice()->create([
            'invoice_number' => $invoiceNumber,
            'issued_at' => $issuedAt,
            'due_date' => $issuedAt->copy()->addDays(config('katering.invoice_due_days')),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
            'status' => $invoiceStatus,
            'paid_at' => $paidAt,
        ]);
    }
}
