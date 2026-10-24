<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CateringController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'city' => ['nullable', Rule::in(config('katering.cities'))],
            'category' => ['nullable', 'exists:categories,slug'],
            'max_price' => ['nullable', 'integer', 'min:0'],
        ]);

        $merchants = Merchant::query()
            ->whereHas('menus', fn ($q) => $q->available())
            ->filter($filters)
            ->withCount(['menus' => fn ($q) => $q->available()])
            ->withMin(['menus' => fn ($q) => $q->available()], 'price')
            ->with([
                'menus' => fn ($q) => $q->available()->with('category')->orderBy('price')->limit(3),
            ])
            ->orderBy('company_name')
            ->paginate(10)
            ->withQueryString();

        // Cities that actually have caterers, so the filter never leads to an empty dead end.
        $cities = Merchant::whereHas('menus', fn ($q) => $q->available())
            ->distinct()->orderBy('city')->pluck('city');

        return view('customer.caterings.index', [
            'merchants' => $merchants,
            'filters' => $filters,
            'cities' => $cities,
            'categories' => Category::orderBy('name')->get(),
            'customerCity' => $request->user()->customer->city,
        ]);
    }

    public function show(Request $request, Merchant $merchant): View
    {
        $menus = $merchant->menus()->available()->with('category')->orderBy('category_id')->orderBy('price')->get();
        abort_if($menus->isEmpty(), 404);

        $customer = $request->user()->customer;

        return view('customer.caterings.show', [
            'merchant' => $merchant,
            'menusByCategory' => $menus->groupBy(fn ($m) => $m->category->name),
            'customer' => $customer,
            'minDate' => today()->addDays(config('katering.min_lead_days'))->toDateString(),
            'slots' => config('katering.delivery_slots'),
            'taxRate' => config('katering.tax_rate'),
        ]);
    }
}
