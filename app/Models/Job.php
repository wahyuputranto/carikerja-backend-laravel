<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'client_profile_id',
        'title',
        'slug',
        'description',
        'requirements',
        'job_category_id',
        'location_id',
        'salary_min',
        'salary_max',
        'quota',
        'deadline',
        'status',
    ];

    protected $casts = [
        'salary_min' => 'decimal:2',
        'salary_max' => 'decimal:2',
        'deadline' => 'date',
        'quota' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($job) {
            if (empty($job->id)) {
                $job->id = (string) Str::uuid();
            }
            if (empty($job->slug)) {
                $job->slug = Str::slug($job->title) . '-' . Str::random(6);
            }
        });
    }

    public function clientProfile(): BelongsTo
    {
        return $this->belongsTo(ClientProfile::class);
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }
}
