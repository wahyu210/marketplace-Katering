<?php

namespace App\Http\Controllers\Merchant;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $merchant = $request->user()->merchant;
        $orders = $merchant->orders();

        $upcoming = $merchant->orders()
            ->with(['customer', 'items'])
            ->whereIn('status', [OrderStatus::Pending, OrderStatus::Confirmed])
            ->whereDate('delivery_date', '>=', today())
            ->orderBy('delivery_date')
            ->orderBy('delivery_time')
            ->take(8)
            ->get();

        $invoiceQuery = fn () => Invoice::whereHas('order', fn ($q) => $q->where('merchant_id', $merchant->id));

        return view('merchant.dashboard', [
            'merchant' => $merchant,
            'upcoming' => $upcoming,
            'stats' => [
                'pending' => (clone $orders)->where('status', OrderStatus::Pending)->count(),
                'today' => (clone $orders)->whereDate('delivery_date', today())
                    ->whereIn('status', [OrderStatus::Confirmed, OrderStatus::Pending])->count(),
                'unpaid' => $invoiceQuery()->where('status', InvoiceStatus::Unpaid)->sum('total'),
                'revenue_month' => $invoiceQuery()->where('status', InvoiceStatus::Paid)
                    ->where('paid_at', '>=', now()->startOfMonth())->sum('total'),
                'menus' => $merchant->menus()->count(),
            ],
            'profileIncomplete' => blank($merchant->description),
        ]);
    }
}
