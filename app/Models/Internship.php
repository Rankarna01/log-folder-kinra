<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Internship extends Model
{
    protected $fillable = [
        'participant_id',
        'mentor_id',
        'institution_id',
        'division',
        'position',
        'start_date',
        'end_date',
        'work_days',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'work_days' => 'array',
        ];
    }

    public function participant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'participant_id');
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function logbooks(): HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function getProgressDaysAttribute(): array
    {
        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        $today = Carbon::today();

        $totalDays = max(1, $start->diffInDays($end) + 1);
        $elapsed = $today->lt($start) ? 1 : min($totalDays, $start->diffInDays($today) + 1);
        $percentage = (int) min(100, round(($elapsed / $totalDays) * 100));

        return [
            'current' => $elapsed,
            'total' => $totalDays,
            'percentage' => $percentage,
        ];
    }
}
