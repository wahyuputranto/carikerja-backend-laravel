<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Job extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'client_profile_id',
        'title',
        'slug',
        'description',
        'requirements',
        'job_category_id',
        'job_location_id',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_profile_id');
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class, 'job_category_id');
    }

    public function jobLocation(): BelongsTo
    {
        return $this->belongsTo(JobLocation::class, 'job_location_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }
}
