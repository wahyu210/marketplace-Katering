<?php

namespace App\Http\Controllers\Merchant;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'status' => ['nullable', Rule::enum(OrderStatus::class)],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $orders = $request->user()->merchant->orders()
            ->with(['customer', 'invoice'])
            ->withSum('items', 'quantity')
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['from'] ?? null, fn ($q, $d) => $q->whereDate('delivery_date', '>=', $d))
            ->when($filters['to'] ?? null, fn ($q, $d) => $q->whereDate('delivery_date', '<=', $d))
            ->when($filters['q'] ?? null, function ($q, $term) {
                $like = '%'.mb_strtolower($term).'%';
                $q->where(fn ($q) => $q->whereRaw('LOWER(order_number) LIKE ?', [$like])
                    ->orWhereHas('customer', fn ($c) => $c->whereRaw('LOWER(office_name) LIKE ?', [$like])));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('merchant.orders.index', [
            'orders' => $orders,
            'filters' => $filters,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order): View
    {
        Gate::authorize('updateStatus', $order);

        return view('merchant.orders.show', [
            'order' => $order->load(['items', 'customer.user', 'invoice']),
        ]);
    }

    public function updateStatus(Request $request, Order $order, OrderService $service): RedirectResponse
    {
        Gate::authorize('updateStatus', $order);

        $data = $request->validate(['status' => ['required', Rule::enum(OrderStatus::class)]]);
        $status = OrderStatus::from($data['status']);

        $service->updateStatus($order, $status);

        return back()->with('status', "Pesanan {$order->order_number}: {$status->label()}.");
    }
}
