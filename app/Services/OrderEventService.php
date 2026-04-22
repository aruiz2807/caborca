<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Support\Facades\Auth;

class OrderEventService
{
    /**
     * Log an event for an order.
     *
     * @param Order $order
     * @param string $eventName
     * @param string|null $description
     * @param int|null $userId
     * @return OrderEvent
     */
    public function log(Order $order, string $eventName, ?string $description = null, ?int $userId = null): OrderEvent
    {
        return OrderEvent::create([
            'order_id' => $order->id,
            'user_id' => $userId ?? Auth::id(),
            'event_name' => $eventName,
            'description' => $description,
        ]);
    }
}
