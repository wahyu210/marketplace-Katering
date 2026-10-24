<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $this->isMerchantOf($user, $order) || $this->isCustomerOf($user, $order);
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $this->isMerchantOf($user, $order);
    }

    public function cancel(User $user, Order $order): bool
    {
        return $this->isCustomerOf($user, $order);
    }

    private function isMerchantOf(User $user, Order $order): bool
    {
        return $user->isMerchant() && $user->merchant?->id === $order->merchant_id;
    }

    private function isCustomerOf(User $user, Order $order): bool
    {
        return $user->isCustomer() && $user->customer?->id === $order->customer_id;
    }
}
