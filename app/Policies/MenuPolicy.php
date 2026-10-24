<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;

class MenuPolicy
{
    public function update(User $user, Menu $menu): bool
    {
        return $user->isMerchant() && $user->merchant?->id === $menu->merchant_id;
    }

    public function delete(User $user, Menu $menu): bool
    {
        return $this->update($user, $menu);
    }
}
