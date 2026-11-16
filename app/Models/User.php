<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'nim_nik',
        'campus',
        'major',
        'avatar_path',
        'signature_path',
        'preferences',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'preferences' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function activeInternship(): HasOne
    {
        return $this->hasOne(Internship::class, 'participant_id')->where('status', 'active')->latestOfMany();
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'participant_id');
    }

    public function mentoredInternships(): HasMany
    {
        return $this->hasMany(Internship::class, 'mentor_id');
    }

    public function aiUsages(): HasMany
    {
        return $this->hasMany(AiUsage::class);
    }

    public function activityTemplates(): HasMany
    {
        return $this->hasMany(ActivityTemplate::class);
    }

    public function isParticipant(): bool
    {
        return $this->role === Role::Participant;
    }

    public function isMentor(): bool
    {
        return $this->role === Role::Mentor;
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));
        $first = mb_substr($parts[0] ?? 'U', 0, 1);
        $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
        return mb_strtoupper($first . $second);
    }
}
