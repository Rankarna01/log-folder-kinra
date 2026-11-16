<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $fillable = [
        'name',
        'address',
        'logo_path',
        'letterhead',
    ];

    protected function casts(): array
    {
        return [
            'letterhead' => 'array',
        ];
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function holidays(): HasMany
    {
        return $this->hasMany(Holiday::class);
    }
}
