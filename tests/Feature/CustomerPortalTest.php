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
use Tests\TestCase;

class CustomerPortalTest extends TestCase
{
    use RefreshDatabase;

    private User $customerUser;

    private Customer $customer;

    private User $merchantUser;

    private Merchant $merchant;

    private Category $category;

    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customerUser = User::factory()->create(['role' => Role::Customer]);
        $this->customer = Customer::create([
            'user_id' => $this->customerUser->id,
            'office_name' => 'PT Maju Terus',
            'city' => 'Jakarta Selatan',
            'address' => 'Gedung Cyber Lt. 8',
            'phone' => '021-12345678',
        ]);

        $this->merchantUser = User::factory()->create(['role' => Role::Merchant]);
        $this->merchant = Merchant::create([
            'user_id' => $this->merchantUser->id,
            'company_name' => 'Katering Barokah',
            'slug' => 'katering-barokah',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Tebet Timur No. 5',
            'phone' => '0812998877',
            'description' => 'Makanan rumahan higienis',
        ]);

        $this->category = Category::create([
            'name' => 'Nasi Kotak',
            'slug' => 'nasi-kotak',
        ]);

        $this->menu = Menu::create([
            'merchant_id' => $this->merchant->id,
            'category_id' => $this->category->id,
            'name' => 'Nasi Ayam Bakar',
            'description' => 'Ayam bakar dengan sambal terasi',
            'price' => 25000,
            'min_order' => 10,
            'photo' => 'menus/ayam-bakar.jpg',
            'is_available' => true,
        ]);
    }

    public function test_customer_can_search_and_filter_caterings(): void
    {
        $response = $this->actingAs($this->customerUser)->get(route('customer.caterings.index', [
            'q' => 'Barokah',
            'city' => 'Jakarta Selatan',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Katering Barokah');
    }

    public function test_customer_can_view_catering_detail(): void
    {
        $response = $this->actingAs($this->customerUser)->get(route('customer.caterings.show', $this->merchant));

        $response->assertStatus(200);
        $response->assertSee('Katering Barokah');
        $response->assertSee('Nasi Ayam Bakar');
        $response->assertSee('25.000');
    }

    public function test_customer_can_place_order_and_invoice_is_generated(): void
    {
        $deliveryDate = today()->addDays(2)->toDateString();

        $response = $this->actingAs($this->customerUser)->post(
            route('customer.orders.store', $this->merchant),
            [
                'items' => [
                    $this->menu->id => 20, // 20 portions * 25,000 = 500,000
                ],
                'delivery_date' => $deliveryDate,
                'delivery_time' => '12:00',
                'delivery_address' => 'Gedung Cyber Lt. 8, Ruang Meeting Utama',
                'notes' => 'Tolong pisahkan sambal',
            ]
        );

        $order = Order::where('customer_id', $this->customer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($this->merchant->id, $order->merchant_id);
        $this->assertEquals($deliveryDate, $order->delivery_date->toDateString());
        $this->assertEquals('12:00', substr($order->delivery_time, 0, 5));
        $this->assertEquals(500000, $order->subtotal);
        $this->assertEquals(OrderStatus::Pending, $order->status);

        $response->assertRedirect(route('customer.orders.show', $order));

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'menu_id' => $this->menu->id,
            'quantity' => 20,
            'unit_price' => 25000,
            'subtotal' => 500000,
        ]);

        $this->assertNotNull($order->invoice);
        $this->assertEquals(500000, $order->invoice->subtotal);
        $this->assertEquals(55000, $order->invoice->tax); // 11% PPN
        $this->assertEquals(555000, $order->invoice->total);
        $this->assertEquals(InvoiceStatus::Unpaid, $order->invoice->status);
    }

    public function test_customer_cannot_place_order_below_min_order(): void
    {
        $deliveryDate = today()->addDays(2)->toDateString();

        $response = $this->actingAs($this->customerUser)->post(
            route('customer.orders.store', $this->merchant),
            [
                'items' => [
                    $this->menu->id => 5, // Minimum order is 10
                ],
                'delivery_date' => $deliveryDate,
                'delivery_time' => '12:00',
                'delivery_address' => 'Gedung Cyber Lt. 8',
            ]
        );

        $response->assertSessionHasErrors();
        $this->assertEquals(0, Order::count());
    }

    public function test_customer_can_view_orders_and_invoices(): void
    {
        $deliveryDate = today()->addDays(3);
        $order = Order::create([
            'order_number' => 'ORD-261023-ABCDE',
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id,
            'delivery_date' => $deliveryDate,
            'delivery_time' => '11:30',
            'delivery_address' => 'Gedung Cyber Lt. 8',
            'status' => OrderStatus::Pending,
            'subtotal' => 250000,
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV/202610/9999',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 250000,
            'tax' => 27500,
            'total' => 277500,
            'status' => InvoiceStatus::Unpaid,
        ]);

        // List orders
        $response = $this->actingAs($this->customerUser)->get(route('customer.orders.index'));
        $response->assertStatus(200);
        $response->assertSee('ORD-261023-ABCDE');

        // Show single order
        $response = $this->actingAs($this->customerUser)->get(route('customer.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('ORD-261023-ABCDE');

        // View invoice
        $response = $this->actingAs($this->customerUser)->get(route('invoices.show', $invoice));
        $response->assertStatus(200);
        $response->assertSee('INV/202610/9999');

        // Download invoice PDF
        $response = $this->actingAs($this->customerUser)->get(route('invoices.pdf', $invoice));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_customer_can_cancel_order(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-261023-CANCL',
            'customer_id' => $this->customer->id,
            'merchant_id' => $this->merchant->id,
            'delivery_date' => today()->addDays(3),
            'delivery_time' => '12:00',
            'delivery_address' => 'Gedung Cyber Lt. 8',
            'status' => OrderStatus::Pending,
            'subtotal' => 250000,
        ]);

        $invoice = Invoice::create([
            'order_id' => $order->id,
            'invoice_number' => 'INV/202610/0002',
            'issued_at' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 250000,
            'tax' => 27500,
            'total' => 277500,
            'status' => InvoiceStatus::Unpaid,
        ]);

        $response = $this->actingAs($this->customerUser)->patch(route('customer.orders.cancel', $order));
        $response->assertSessionHas('status');

        $order->refresh();
        $this->assertEquals(OrderStatus::Cancelled, $order->status);

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::Void, $invoice->status);
    }

    public function test_customer_can_update_profile(): void
    {
        $response = $this->actingAs($this->customerUser)->put(route('customer.profile.update'), [
            'name' => 'Ahmad HR Manager',
            'office_name' => 'PT Maju Terus Bersama',
            'city' => 'Jakarta Pusat',
            'address' => 'Jl. Jenderal Sudirman Kav. 21',
            'phone' => '021-99881122',
        ]);

        $response->assertRedirect(route('customer.profile.edit'));
        $this->customer->refresh();
        $this->assertEquals('PT Maju Terus Bersama', $this->customer->office_name);
        $this->assertEquals('Jakarta Pusat', $this->customer->city);
    }
}
