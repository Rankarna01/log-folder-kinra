<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    protected $fillable = [
        'logbook_id',
        'path',
        'thumb_path',
        'mime',
        'size_kb',
        'caption',
        'width',
        'height',
        'taken_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
        ];
    }

    public function logbook(): BelongsTo
    {
        return $this->belongsTo(Logbook::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
