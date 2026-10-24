<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $role = $request->query('as') === Role::Merchant->value ? Role::Merchant : Role::Customer;

        return view('auth.register', [
            'role' => $role,
            'cities' => config('katering.cities'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::enum(Role::class)],
            'company_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,}$/'],
            'city' => ['required', Rule::in(config('katering.cities'))],
            'address' => ['required', 'string', 'max:500'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'company_name' => $request->input('role') === Role::Merchant->value ? 'nama katering' : 'nama kantor',
            'name' => 'nama penanggung jawab',
            'phone' => 'nomor telepon',
            'city' => 'kota',
            'address' => 'alamat',
        ]);

        $user = DB::transaction(function () use ($data) {
            $role = Role::from($data['role']);

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $role,
            ]);

            $profile = [
                'city' => $data['city'],
                'address' => $data['address'],
                'phone' => $data['phone'],
            ];

            if ($role === Role::Merchant) {
                $user->merchant()->create($profile + ['company_name' => $data['company_name']]);
            } else {
                $user->customer()->create($profile + ['office_name' => $data['company_name']]);
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        $message = $user->isMerchant()
            ? 'Akun katering dibuat. Lengkapi profil dan tambahkan menu pertama Anda.'
            : 'Akun kantor dibuat. Silakan cari katering untuk makan siang tim.';

        return redirect()->route($user->isMerchant() ? 'merchant.profile.edit' : $user->homeRoute())
            ->with('status', $message);
    }
}
