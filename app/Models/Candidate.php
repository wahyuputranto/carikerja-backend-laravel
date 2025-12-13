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

    public function cv()
    {
        return $this->hasOne(CandidateCv::class)->latest('uploaded_at');
    }

    public function preInterviews()
    {
        return $this->hasMany(PreInterview::class);
    }

    /**
     * Check if candidate is ready to hire and update status accordingly.
     * Conditions:
     * 1. Status is NOT_AVAILABLE or AVAILABLE
     * 2. All Mandatory Documents are VALID
     * 3. CV is VALID (Approved)
     * 4. Pre-Interview is PASSED
     */
    /**
     * Check if candidate meets all requirements and sync hiring status.
     * Upgrades to READY_TO_HIRE if all met.
     * Downgrades to AVAILABLE if current is READY_TO_HIRE but requirements not met.
     */
    public function syncHiringStatus()
    {
        // Never change HIRED status automatically
        if ($this->hiring_status === 'HIRED') {
            return;
        }

        $isReady = true;

        // 0. Check Profile Completeness (Basic fields)
        $profile = $this->profile;
        if (!$profile || empty($profile->nik) || empty($profile->birth_date) || empty($profile->address) || empty($profile->city)) {
            $isReady = false;
        }

        // 1. Check CV Approval
        if ($isReady) {
            $cv = $this->cv;
            if (!$cv || $cv->status !== 'VALID') {
                $isReady = false;
            }
        }

        // 2. Check Mandatory Documents
        if ($isReady) {
            $mandatoryTypes = \App\Models\DocumentType::where('is_mandatory', true)
                ->where('slug', '!=', 'cv')
                ->pluck('id');
            
            $validDocsCount = $this->documents()
                ->whereIn('document_type_id', $mandatoryTypes)
                ->where('status', 'VALID')
                ->distinct('document_type_id') // Ensure unique types
                ->count('document_type_id');
            
            if ($validDocsCount < $mandatoryTypes->count()) {
                $isReady = false;
            }
        }

        // 3. Check Pre-Interview Passed
        if ($isReady) {
            $preInterviewPassed = $this->preInterviews()
                ->where('result', 'PASSED')
                ->exists();

            if (!$preInterviewPassed) {
                $isReady = false;
            }
        }

        // Apply Status Update
        if ($isReady) {
            if ($this->hiring_status !== 'READY_TO_HIRE') {
                $this->update(['hiring_status' => 'READY_TO_HIRE']);
            }
        } else {
            // Downgrade if currently READY_TO_HIRE but no longer ready
            if ($this->hiring_status === 'READY_TO_HIRE') {
                $this->update(['hiring_status' => 'AVAILABLE']);
            }
        }
    }

    /**
     * Alias for backward compatibility
     */
    public function updateHiringStatusIfReady()
    {
        $this->syncHiringStatus();
    }
}
