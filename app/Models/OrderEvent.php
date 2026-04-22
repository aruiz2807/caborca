<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderEvent extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'event_name',
        'description',
    ];

    /**
     * Get the order that owns the event.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the user that performed the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
