<?php

namespace App\Enums;

enum Role: string
{
    case Merchant = 'merchant';
    case Customer = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::Merchant => 'Katering',
            self::Customer => 'Kantor',
        };
    }
}
