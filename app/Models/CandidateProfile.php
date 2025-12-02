<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CandidateProfile extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'candidate_id',
        'nik',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'city',
        'province',
        'postal_code',
        'about_me',
        'photo_url',
        'cv_url',
        'interested_job_category_id',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($profile) {
            if (empty($profile->id)) {
                $profile->id = (string) Str::uuid();
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function interestedJobCategory()
    {
        return $this->belongsTo(JobCategory::class, 'interested_job_category_id');
    }
}
