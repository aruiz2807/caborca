<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'location_id',
        'database',
    ];

    /**
     * Get the orders for the workshop.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'appointment_workshop_id');
    }

    /**
     * Get the location that owns the workshop.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
