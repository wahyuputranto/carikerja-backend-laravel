<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Job extends Model
{
    use HasFactory, HasUuids;

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
        'status',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}
