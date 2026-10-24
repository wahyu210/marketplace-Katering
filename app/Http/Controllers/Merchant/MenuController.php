<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(Request $request): View
    {
        $merchant = $request->user()->merchant;

        $menus = $merchant->menus()
            ->with('category')
            ->when($request->query('q'), fn ($q, $term) => $q->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($term).'%']))
            ->orderByDesc('is_available')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('merchant.menus.index', compact('menus'));
    }

    public function create(): View
    {
        return view('merchant.menus.form', [
            'menu' => new Menu(['is_available' => true, 'min_order' => 10]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, photoRequired: true);
        $data['photo'] = $request->file('photo')->store('menus', 'public');

        $request->user()->merchant->menus()->create($data);

        return redirect()->route('merchant.menus.index')->with('status', "Menu \"{$data['name']}\" ditambahkan.");
    }

    public function edit(Menu $menu): View
    {
        Gate::authorize('update', $menu);

        return view('merchant.menus.form', [
            'menu' => $menu,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        Gate::authorize('update', $menu);

        $data = $this->validated($request, photoRequired: false);

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($menu->photo);
            $data['photo'] = $request->file('photo')->store('menus', 'public');
        } else {
            unset($data['photo']);
        }

        $menu->update($data);

        return redirect()->route('merchant.menus.index')->with('status', "Menu \"{$menu->name}\" diperbarui.");
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        Gate::authorize('delete', $menu);

        // Order history keeps a name/price snapshot, so deleting is safe.
        Storage::disk('public')->delete($menu->photo);
        $menu->delete();

        return redirect()->route('merchant.menus.index')->with('status', "Menu \"{$menu->name}\" dihapus.");
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, bool $photoRequired): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string', 'max:1000'],
            'price' => ['required', 'integer', 'min:1000', 'max:10000000'],
            'min_order' => ['required', 'integer', 'min:1', 'max:10000'],
            'photo' => [$photoRequired ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_available' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama menu',
            'category_id' => 'jenis makanan',
            'description' => 'deskripsi',
            'price' => 'harga',
            'min_order' => 'minimal order',
            'photo' => 'foto',
        ]);

        $data['is_available'] = $request->boolean('is_available');

        return $data;
    }
}
