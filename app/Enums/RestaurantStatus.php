<?php

namespace App\Enums;

enum RestaurantStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }

    /**
     * Check if the restaurant can accept orders with this status.
     */
    public function canAcceptOrders(): bool
    {
        return $this === self::ACTIVE;
    }
}
