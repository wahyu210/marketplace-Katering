<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu konfirmasi',
            self::Confirmed => 'Dikonfirmasi',
            self::Delivered => 'Terkirim',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /** CSS modifier used by the status tag component. */
    public function tone(): string
    {
        return match ($this) {
            self::Pending => 'warn',
            self::Confirmed => 'info',
            self::Delivered => 'ok',
            self::Cancelled => 'muted',
        };
    }

    /**
     * Statuses the merchant may move this order to.
     *
     * @return list<self>
     */
    public function nextForMerchant(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Cancelled],
            self::Confirmed => [self::Delivered, self::Cancelled],
            default => [],
        };
    }
}
