<?php

namespace App\Enums;

enum OrderStatus: int
{
    case REQUESTED = 1;
    case SCHEDULED = 2;
    case ENTERED = 3;
    case FINISHED = 4;
    case NO_SHOW = 5;

    public function label(): string
    {
        return match($this) {
            self::REQUESTED => __('pages.orders.status_requested'),
            self::SCHEDULED => __('pages.orders.status_scheduled'),
            self::ENTERED => __('pages.orders.status_entered'),
            self::FINISHED => __('pages.orders.status_finished'),
            self::NO_SHOW => __('pages.orders.status_no_show'),
        };
    }
}
