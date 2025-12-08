<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'province',
        'city',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the jobs for this location
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get full location string
     */
    public function getFullLocationAttribute(): string
    {
        return "{$this->city}, {$this->province}, {$this->country}";
    }

    /**
     * Scope to filter by country
     */
    public function scopeByCountry($query, string $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to filter active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
