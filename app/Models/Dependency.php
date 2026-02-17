<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
     * Get the dependency's location name
     */
    protected function getLocationAttribute()
    {
        $location = Location::find($this->location_id);

        return empty($location) ? 'N/D' : $location->name;
    }

    /**
     * Get the dependency's user name
     */
    protected function getUserAttribute()
    {
        $user = User::find($this->user_id);

        return empty($user) ? 'N/D' : $user->name;
    }
}
