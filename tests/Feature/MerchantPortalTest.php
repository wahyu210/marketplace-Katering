<?php

namespace Tests\Feature;

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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MerchantPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $merchantUser;

    private Merchant $merchant;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->merchantUser = User::factory()->create(['role' => Role::Merchant]);
        $this->merchant = Merchant::create([
            'user_id' => $this->merchantUser->id,
            'company_name' => 'Sedap Catering',
            'slug' => 'sedap-catering',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Tebet No. 10',
            'phone' => '0812345678',
            'description' => 'Katering lezat',
        ]);

        $this->category = Category::create([
            'name' => 'Nasi Kotak',
            'slug' => 'nasi-kotak',
        ]);
    }

    public function test_merchant_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->merchantUser)->get(route('merchant.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Sedap Catering');
    }

    public function test_merchant_can_update_profile(): void
    {
        $response = $this->actingAs($this->merchantUser)->put(route('merchant.profile.update'), [
            'name' => 'Budi Santoso Updated',
            'company_name' => 'Sedap Catering Premium',
            'city' => 'Jakarta Pusat',
            'address' => 'Jl. Thamrin No. 20',
            'phone' => '08987654321',
            'description' => 'Deskripsi baru yang lebih lengkap',
        ]);

        $response->assertRedirect(route('merchant.profile.edit'));
        $this->merchant->refresh();
        $this->assertEquals('Sedap Catering Premium', $this->merchant->company_name);
        $this->assertEquals('Jakarta Pusat', $this->merchant->city);
    }

    public function test_merchant_can_create_menu(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image('menu.jpg', 600, 400);

        $response = $this->actingAs($this->merchantUser)->post(route('merchant.menus.store'), [
            'name' => 'Paket Ayam Geprek',
            'category_id' => $this->category->id,
            'description' => 'Ayam geprek pedas gurih dengan lalapan',
            'price' => 25000,
            'min_order' => 10,
            'photo' => $photo,
            'is_available' => '1',
        ]);

        $response->assertRedirect(route('merchant.menus.index'));
        $this->assertDatabaseHas('menus', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Paket Ayam Geprek',
            'price' => 25000,
            'min_order' => 10,
        ]);

        $menu = Menu::where('name', 'Paket Ayam Geprek')->first();
        Storage::disk('public')->assertExists($menu->photo);
    }

    public function test_merchant_can_update_menu(): void
    {
        Storage::fake('public');

        $menu = Menu::create([
            'merchant_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'name' => 'Nasi Rendang',
            'description' => 'Rendang daging sapi',
            'price' => 35000,
            'min_order' => 5,
            'photo' => 'menus/sample.jpg',
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->merchantUser)->put(route('merchant.menus.update', $menu), [
            'name' => 'Nasi Rendang Spesial',
            'category_id' => $this->category->id,
            'description' => 'Rendang daging sapi khas Minang',
            'price' => 38000,
            'min_order' => 5,
            'is_available' => '1',
        ]);

        $response->assertRedirect(route('merchant.menus.index'));
        $menu->refresh();
        $this->assertEquals('Nasi Rendang Spesial', $menu->name);
        $this->assertEquals(38000, $menu->price);
    }

    public function test_merchant_can_delete_menu(): void
    {
        Storage::fake('public');

        $menu = Menu::create([
            'merchant_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'name' => 'Menu To Delete',
            'description' => 'Akan dihapus',
            'price' => 20000,
            'min_order' => 5,
            'photo' => 'menus/dummy.jpg',
            'is_available' => true,
        ]);

        $response = $this->actingAs($this->merchantUser)->delete(route('merchant.menus.destroy', $menu));
        $response->assertRedirect(route('merchant.menus.index'));
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
    }

    public function test_merchant_can_view_orders_and_update_status(): void
    {
        $customerUser = User::factory()->create(['role' => Role::Customer]);
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'office_name' => 'PT Pelanggan',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Sudirman',
            'phone' => '0211111',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'merchant_id' => $this->merchant->id,
            'delivery_date' => today()->addDays(2),
            'delivery_time' => '12:00',
            'delivery_address' => 'Jl. Sudirman Lt 3',
            'status' => OrderStatus::Pending,
            'subtotal' => 250000,
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV/202610/0001',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 250000,
            'tax' => 27500,
            'total' => 277500,
            'status' => InvoiceStatus::Unpaid,
        ]);

        // View orders list
        $response = $this->actingAs($this->merchantUser)->get(route('merchant.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-001');

        // View single order
        $response = $this->actingAs($this->merchantUser)->get(route('merchant.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-001');

        // Confirm order
        $response = $this->actingAs($this->merchantUser)->patch(route('merchant.orders.status', $order), [
            'status' => OrderStatus::Confirmed->value,
        ]);
        $response->assertRedirect(route('merchant.orders.show', $order));
        $order->refresh();
        $this->assertEquals(OrderStatus::Confirmed, $order->status);

        // Mark invoice as paid
        $response = $this->actingAs($this->merchantUser)->patch(route('invoices.paid', $invoice));
        $response->assertSessionHas('status');
        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::Paid, $invoice->status);
        $this->assertNotNull($invoice->paid_at);
    }
}
