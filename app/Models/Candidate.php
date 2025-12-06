<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Notifications\Notifiable;

class Candidate extends Model
{
    use HasFactory, Notifiable;

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
        'interested_job_category_id',
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

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function personalDetail()
    {
        return $this->hasOne(CandidatePersonalDetail::class);
    }

    public function passports()
    {
        return $this->hasMany(CandidatePassport::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(CandidateEmergencyContact::class);
    }

    public function nonFormalEducations()
    {
        return $this->hasMany(CandidateNonFormalEducation::class);
    }

    public function languages()
    {
        return $this->hasMany(CandidateLanguage::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function computerSkills()
    {
        return $this->hasMany(CandidateComputerSkill::class);
    }
}
