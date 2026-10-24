<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSee('Buat akun');
    }

    public function test_merchant_can_register(): void
    {
        $response = $this->post('/register', [
            'role' => Role::Merchant->value,
            'name' => 'Owner Katering',
            'email' => 'owner@katering.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'company_name' => 'Berkah Rasa Catering',
            'city' => 'Jakarta Selatan',
            'phone' => '081234567890',
            'address' => 'Jl. Tebet Barat No. 12',
            'description' => 'Spesialis katering harian kantor',
        ]);

        $response->assertRedirect(route('merchant.profile.edit'));
        $this->assertAuthenticated();

        $user = User::where('email', 'owner@katering.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Role::Merchant, $user->role);
        $this->assertNotNull($user->merchant);
        $this->assertEquals('Berkah Rasa Catering', $user->merchant->company_name);
        $this->assertEquals('berkah-rasa-catering', $user->merchant->slug);
    }

    public function test_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'role' => Role::Customer->value,
            'name' => 'Admin HR PT Sejahtera',
            'email' => 'hr@sejahtera.test',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'company_name' => 'PT Sejahtera Abadi',
            'city' => 'Jakarta Pusat',
            'phone' => '021-99887766',
            'address' => 'Sudirman Central Business District Tower A',
        ]);

        $response->assertRedirect(route('customer.caterings.index'));
        $this->assertAuthenticated();

        $user = User::where('email', 'hr@sejahtera.test')->first();
        $this->assertNotNull($user);
        $this->assertEquals(Role::Customer, $user->role);
        $this->assertNotNull($user->customer);
        $this->assertEquals('PT Sejahtera Abadi', $user->customer->office_name);
    }

    public function test_customer_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));
        $response->assertStatus(200);
        $response->assertSee('Masuk Akun Kantor');
        $response->assertSee('Portal Kantor');
    }

    public function test_merchant_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('merchant.login'));
        $response->assertStatus(200);
        $response->assertSee('Masuk Akun Katering');
        $response->assertSee('Portal Katering');
    }

    public function test_customer_can_login_via_customer_portal(): void
    {
        $customerUser = User::factory()->create([
            'role' => Role::Customer,
            'password' => 'password123',
        ]);
        $customerUser->customer()->create([
            'office_name' => 'PT Maju Terus',
            'city' => 'Jakarta Selatan',
            'address' => 'Jl. Sudirman No. 1',
            'phone' => '021-123456',
        ]);

        $response = $this->post(route('login'), [
            'email' => $customerUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('customer.caterings.index'));
        $this->assertAuthenticatedAs($customerUser);
    }

    public function test_merchant_can_login_via_merchant_portal(): void
    {
        $merchantUser = User::factory()->create([
            'role' => Role::Merchant,
            'password' => 'password123',
        ]);
        $merchantUser->merchant()->create([
            'company_name' => 'Enak Poll',
            'slug' => 'enak-poll',
            'city' => 'Bandung',
            'phone' => '08123',
            'address' => 'Jl. Riau No. 1',
        ]);

        $response = $this->post(route('merchant.login'), [
            'email' => $merchantUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('merchant.dashboard'));
        $this->assertAuthenticatedAs($merchantUser);
    }

    public function test_merchant_cannot_login_on_customer_portal(): void
    {
        $merchantUser = User::factory()->create([
            'role' => Role::Merchant,
            'password' => 'password123',
        ]);

        $response = $this->post(route('login'), [
            'email' => $merchantUser->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_customer_cannot_login_on_merchant_portal(): void
    {
        $customerUser = User::factory()->create([
            'role' => Role::Customer,
            'password' => 'password123',
        ]);

        $response = $this->post(route('merchant.login'), [
            'email' => $customerUser->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_unauthenticated_merchant_route_redirects_to_merchant_login(): void
    {
        $response = $this->get('/merchant/pesanan');
        $response->assertRedirect(route('merchant.login'));
    }

    public function test_unauthenticated_customer_route_redirects_to_customer_login(): void
    {
        $response = $this->get('/kantor/pesanan');
        $response->assertRedirect(route('login'));
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => Role::Customer,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
