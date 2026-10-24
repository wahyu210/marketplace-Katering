<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['order_id', 'invoice_number', 'issued_at', 'due_date', 'subtotal', 'tax', 'total', 'status', 'paid_at'])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'status' => InvoiceStatus::class,
            'subtotal' => 'integer',
            'tax' => 'integer',
            'total' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'invoice_number';
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Invoice numbers contain slashes, so URLs use a dash-separated form. */
    public function urlKey(): string
    {
        return str_replace('/', '-', $this->invoice_number);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('invoice_number', str_replace('-', '/', $value))->firstOrFail();
    }

    public function getRouteKey(): string
    {
        return $this->urlKey();
    }
}
