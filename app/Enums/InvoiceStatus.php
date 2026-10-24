<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Void = 'void';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum dibayar',
            self::Paid => 'Lunas',
            self::Void => 'Batal',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Unpaid => 'warn',
            self::Paid => 'ok',
            self::Void => 'muted',
        };
    }
}
