<?php

namespace App\Policies;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function __construct(private OrderPolicy $orders) {}

    public function view(User $user, Invoice $invoice): bool
    {
        return $this->orders->view($user, $invoice->order);
    }

    public function markPaid(User $user, Invoice $invoice): bool
    {
        return $this->orders->updateStatus($user, $invoice->order)
            && $invoice->status === InvoiceStatus::Unpaid;
    }
}
