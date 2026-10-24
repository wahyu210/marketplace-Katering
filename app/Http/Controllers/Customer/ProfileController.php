<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('customer.profile', [
            'customer' => $request->user()->customer,
            'user' => $request->user(),
            'cities' => config('katering.cities'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'office_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,}$/'],
            'city' => ['required', Rule::in(config('katering.cities'))],
            'address' => ['required', 'string', 'max:500'],
        ], [], [
            'office_name' => 'nama kantor',
            'name' => 'nama penanggung jawab',
            'phone' => 'nomor telepon',
            'city' => 'kota',
            'address' => 'alamat',
        ]);

        $request->user()->customer->update(collect($data)->except('name')->all());
        $request->user()->update(['name' => $data['name']]);

        return redirect()->route('customer.profile.edit')->with('status', 'Profil kantor disimpan.');
    }
}
