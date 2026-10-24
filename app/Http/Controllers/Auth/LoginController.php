<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Tampilkan halaman login khusus Kantor (Customer) */
    public function createCustomer(): View
    {
        return view('auth.login-customer');
    }

    /** Tampilkan halaman login khusus Katering (Merchant) */
    public function createMerchant(): View
    {
        return view('auth.login-merchant');
    }

    /** Proses login dari portal Kantor */
    public function storeCustomer(Request $request): RedirectResponse
    {
        return $this->authenticate($request, expectedRole: Role::Customer);
    }

    /** Proses login dari portal Katering */
    public function storeMerchant(Request $request): RedirectResponse
    {
        return $this->authenticate($request, expectedRole: Role::Merchant);
    }

    private function authenticate(Request $request, Role $expectedRole): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($credentials['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.',
            ]);
        }

        // Cek apakah kredensial email & password valid
        if (! Auth::validate($credentials)) {
            RateLimiter::hit($key);
            throw ValidationException::withMessages(['email' => 'Email atau password salah.']);
        }

        $user = User::where('email', $credentials['email'])->first();

        // Validasi kecocokan portal login dengan role pengguna
        if ($user->role !== $expectedRole) {
            RateLimiter::hit($key);
            if ($expectedRole === Role::Customer) {
                throw ValidationException::withMessages([
                    'email' => 'Akun ini terdaftar sebagai Katering (Merchant). Silakan gunakan halaman login Portal Katering.',
                ]);
            } else {
                throw ValidationException::withMessages([
                    'email' => 'Akun ini terdaftar sebagai Kantor (Customer). Silakan gunakan halaman login Portal Kantor.',
                ]);
            }
        }

        Auth::login($user, $request->boolean('remember'));
        RateLimiter::clear($key);
        $request->session()->regenerate();

        return redirect()->intended(route($user->homeRoute()));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
