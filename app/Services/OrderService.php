<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Merchant;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Create an order with its line items and invoice in one transaction.
     *
     * @param  array<int|string, int|string|null>  $quantities  menu_id => portions
     * @param  array{delivery_date: string, delivery_time: string, delivery_address: string, notes?: ?string}  $delivery
     */
    public function place(Customer $customer, Merchant $merchant, array $quantities, array $delivery): Order
    {
        $quantities = collect($quantities)
            ->map(fn ($q) => (int) $q)
            ->filter(fn (int $q) => $q > 0);

        if ($quantities->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Pilih minimal satu menu.']);
        }

        $menus = $merchant->menus()
            ->available()
            ->whereIn('id', $quantities->keys())
            ->get()
            ->keyBy('id');

        $errors = [];
        foreach ($quantities as $menuId => $qty) {
            $menu = $menus->get($menuId);
            if (! $menu) {
                $errors["items.$menuId"] = 'Menu tidak tersedia.';
            } elseif ($qty < $menu->min_order) {
                $errors["items.$menuId"] = "Minimal pesan {$menu->min_order} porsi untuk {$menu->name}.";
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return DB::transaction(function () use ($customer, $merchant, $quantities, $menus, $delivery) {
            $lines = $quantities->map(fn (int $qty, $menuId) => [
                'menu_id' => $menuId,
                'menu_name' => $menus[$menuId]->name,
                'unit_price' => $menus[$menuId]->price,
                'quantity' => $qty,
                'subtotal' => $menus[$menuId]->price * $qty,
            ])->values();

            $subtotal = (int) $lines->sum('subtotal');

            $order = Order::create([
                'order_number' => $this->nextOrderNumber(),
                'customer_id' => $customer->id,
                'merchant_id' => $merchant->id,
                'delivery_date' => $delivery['delivery_date'],
                'delivery_time' => $delivery['delivery_time'],
                'delivery_address' => $delivery['delivery_address'],
                'notes' => $delivery['notes'] ?? null,
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
            ]);

            $order->items()->createMany($lines->all());

            $tax = (int) round($subtotal * config('katering.tax_rate'));
            $issuedAt = now();

            $order->invoice()->create([
                'invoice_number' => $this->nextInvoiceNumber($issuedAt),
                'issued_at' => $issuedAt,
                'due_date' => $issuedAt->copy()->addDays(config('katering.invoice_due_days'))->toDateString(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $subtotal + $tax,
                'status' => InvoiceStatus::Unpaid,
            ]);

            return $order->load('items', 'invoice');
        });
    }

    public function updateStatus(Order $order, OrderStatus $status): void
    {
        if (! in_array($status, $order->status->nextForMerchant(), true)) {
            throw ValidationException::withMessages([
                'status' => "Status tidak bisa diubah dari \"{$order->status->label()}\" ke \"{$status->label()}\".",
            ]);
        }

        DB::transaction(function () use ($order, $status) {
            $order->update(['status' => $status]);

            if ($status === OrderStatus::Cancelled && $order->invoice?->status === InvoiceStatus::Unpaid) {
                $order->invoice->update(['status' => InvoiceStatus::Void]);
            }
        });
    }

    public function cancelByCustomer(Order $order): void
    {
        if ($order->status !== OrderStatus::Pending) {
            throw ValidationException::withMessages(['status' => 'Pesanan yang sudah dikonfirmasi tidak bisa dibatalkan.']);
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => OrderStatus::Cancelled]);
            $order->invoice?->update(['status' => InvoiceStatus::Void]);
        });
    }

    private function nextOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    private function nextInvoiceNumber(Carbon $date): string
    {
        $prefix = 'INV/'.$date->format('Ym').'/';

        // Serialize numbering per month so concurrent checkouts don't collide.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('SELECT pg_advisory_xact_lock(?)', [crc32($prefix)]);
        }

        $last = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $seq = $last ? ((int) Str::afterLast($last, '/')) + 1 : 1;

        return $prefix.str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
