<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * The attributes that are appended when model is retrieved
     *
     * @var array<int, string>
     */
    protected $appends = [
        'location',
    ];

    /**
     * Get the workshop's location name
     */
    protected function getLocationAttribute()
    {
        $location = Location::find($this->location_id);

        return empty($location) ? 'N/D' : $location->name;
    }
}
