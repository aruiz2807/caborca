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
}
