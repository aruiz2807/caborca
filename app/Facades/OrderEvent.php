<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\OrderEvent log(\App\Models\Order $order, string $eventName, ?string $description = null, ?int $userId = null)
 *
 * @see \App\Services\OrderEventService
 */
class OrderEvent extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'order-event';
    }
}
