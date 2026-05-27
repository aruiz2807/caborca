<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(BiReport::class)->orderBy('name');
    }

    public function permissionName(): string
    {
        return static::permissionNameFor($this->id);
    }

    public static function permissionNameFor(int $id): string
    {
        return "view-bi-section-{$id}";
    }
}

