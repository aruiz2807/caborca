<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'bi_section_id',
        'name',
        'slug',
        'embed_url',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(BiSection::class, 'bi_section_id');
    }

    public function permissionName(): string
    {
        return static::permissionNameFor($this->id);
    }

    public static function permissionNameFor(int $id): string
    {
        return "view-bi-report-{$id}";
    }
}

