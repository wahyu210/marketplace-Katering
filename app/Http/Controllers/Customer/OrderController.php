<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
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
        $status = $request->validate(['status' => ['nullable', Rule::enum(OrderStatus::class)]])['status'] ?? null;

        $orders = $request->user()->customer->orders()
            ->with(['merchant', 'invoice'])
            ->withSum('items', 'quantity')
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('customer.orders.index', [
            'orders' => $orders,
            'status' => $status,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function store(Request $request, Merchant $merchant, OrderService $service): RedirectResponse
    {
        $minDate = today()->addDays(config('katering.min_lead_days'))->toDateString();

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'delivery_date' => ['required', 'date', 'after_or_equal:'.$minDate],
            'delivery_time' => ['required', Rule::in(config('katering.delivery_slots'))],
            'delivery_address' => ['required', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'delivery_date.after_or_equal' => 'Tanggal pengiriman paling cepat '.tanggal($minDate).'.',
        ], [
            'delivery_date' => 'tanggal pengiriman',
            'delivery_time' => 'jam pengiriman',
            'delivery_address' => 'alamat pengiriman',
        ]);

        $order = $service->place($request->user()->customer, $merchant, $data['items'], $data);

        return redirect()->route('customer.orders.show', $order)
            ->with('status', "Pesanan dibuat. Invoice {$order->invoice->invoice_number} sudah terbit.");
    }

    public function show(Order $order): View
    {
        Gate::authorize('cancel', $order);

        return view('customer.orders.show', [
            'order' => $order->load(['items', 'merchant', 'invoice']),
        ]);
    }

    public function cancel(Order $order, OrderService $service): RedirectResponse
    {
        Gate::authorize('cancel', $order);

        $service->cancelByCustomer($order);

        return back()->with('status', "Pesanan {$order->order_number} dibatalkan.");
    }
}
