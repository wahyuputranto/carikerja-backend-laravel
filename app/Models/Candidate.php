<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'email_verified_at',
        'phone_verified_at',
        'hiring_status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($candidate) {
            if (empty($candidate->id)) {
                $candidate->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function profile()
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function educations()
    {
        return $this->hasMany(CandidateEducation::class);
    }

    public function experiences()
    {
        return $this->hasMany(CandidateExperience::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }
}
