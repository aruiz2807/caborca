<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'customer_number',
        'location_id',
        'user_id',
    ];

    /**
     * The attributes that are appended when model is retrieved
     *
     * @var array<int, string>
     */
    protected $appends = [
        'location',
        'user',
    ];

    /**
     * Get the location that owns the dependency.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the user that owns the dependency.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the dependency's location name
     */
    protected function getLocationAttribute()
    {
        return $this->location?->name ?? 'N/D';
    }

    /**
     * Get the dependency's user name
     */
    protected function getUserAttribute()
    {
        return $this->user?->name ?? 'N/D';
    }
}
