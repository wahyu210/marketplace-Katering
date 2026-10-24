<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['order_number', 'customer_id', 'merchant_id', 'delivery_date', 'delivery_time', 'delivery_address', 'notes', 'status', 'subtotal'])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'delivery_date' => 'date',
            'status' => OrderStatus::class,
            'subtotal' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function totalPortions(): int
    {
        return (int) $this->items->sum('quantity');
    }

    public function deliveryTimeLabel(): string
    {
        return substr((string) $this->delivery_time, 0, 5);
    }
}
