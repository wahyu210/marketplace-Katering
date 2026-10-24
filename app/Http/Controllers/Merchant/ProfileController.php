<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('merchant.profile', [
            'merchant' => $request->user()->merchant,
            'user' => $request->user(),
            'cities' => config('katering.cities'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $merchant = $user->merchant;

        $data = $request->validate([
            'company_name' => ['required', 'string', 'max:120'],
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,}$/'],
            'city' => ['required', Rule::in(config('katering.cities'))],
            'address' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [], [
            'company_name' => 'nama katering',
            'name' => 'nama penanggung jawab',
            'phone' => 'nomor telepon',
            'city' => 'kota',
            'address' => 'alamat',
            'description' => 'deskripsi',
        ]);

        if ($request->hasFile('logo')) {
            if ($merchant->logo) {
                Storage::disk('public')->delete($merchant->logo);
            }
            $merchant->logo = $request->file('logo')->store('logos', 'public');
        } elseif ($request->boolean('remove_logo') && $merchant->logo) {
            Storage::disk('public')->delete($merchant->logo);
            $merchant->logo = null;
        }

        $merchant->fill(collect($data)->only(['company_name', 'phone', 'city', 'address', 'description'])->all())->save();
        $user->update(['name' => $data['name']]);

        return redirect()->route('merchant.profile.edit')->with('status', 'Profil disimpan.');
    }
}
