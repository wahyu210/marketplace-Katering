<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Merchant;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $merchants = Merchant::query()
            ->whereHas('menus', fn ($q) => $q->available())
            ->withCount(['menus' => fn ($q) => $q->available()])
            ->withMin(['menus' => fn ($q) => $q->available()], 'price')
            ->with(['menus' => fn ($q) => $q->available()->latest()->limit(1)])
            ->latest()
            ->take(6)
            ->get();

        return view('home', [
            'merchants' => $merchants,
            'categories' => Category::orderBy('name')->get(),
            'stats' => [
                'merchants' => Merchant::count(),
                'cities' => Merchant::distinct('city')->count('city'),
            ],
        ]);
    }
}
