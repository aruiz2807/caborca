<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';

    /**
     * Get the translation for the enum value.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('app.active'),
            self::INACTIVE => __('app.inactive'),
        };
    }
}
