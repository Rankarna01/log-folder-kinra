<?php

namespace App\Models;

use App\Enums\LogCategory;
use App\Enums\LogStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logbook extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'internship_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'title',
        'category',
        'raw_description',
        'ai_description',
        'final_description',
        'output',
        'obstacles',
        'work_location',
        'lat',
        'lng',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'source',
        'client_uuid',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status' => LogStatus::class,
            'category' => LogCategory::class,
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class)->orderBy('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isEditable(): bool
    {
        return true;
    }

    public function getLocationLabelAttribute(): string
    {
        return in_array(strtolower((string) $this->work_location), ['lapangan', 'field'], true)
            ? 'Lapangan'
            : 'Kantor';
    }

    public function getLocationIconAttribute(): string
    {
        return $this->location_label === 'Lapangan'
            ? 'fa-solid fa-location-dot'
            : 'fa-solid fa-building';
    }

    public function getFormattedDurationAttribute(): string
    {
        $hours = intdiv($this->duration_minutes, 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}m";
        }
        if ($hours > 0) {
            return "{$hours}h 00m";
        }

        return "{$minutes}m";
    }
}
