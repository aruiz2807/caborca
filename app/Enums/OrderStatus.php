<?php

namespace App\Enums;

enum OrderStatus: int
{
    case REQUESTED = 1;
    case PARTS = 2;
    case PARTS_AVAILABLE = 3;
    case SCHEDULED = 4;
    case ENTERED = 5;
    case FINISHED = 6;
    case NO_SHOW = 7;

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => __('pages.orders.status_requested'),
            self::PARTS => __('pages.orders.status_parts'),
            self::PARTS_AVAILABLE => __('pages.orders.status_parts_available'),
            self::SCHEDULED => __('pages.orders.status_scheduled'),
            self::ENTERED => __('pages.orders.status_entered'),
            self::FINISHED => __('pages.orders.status_finished'),
            self::NO_SHOW => __('pages.orders.status_no_show'),
        };
    }
}
