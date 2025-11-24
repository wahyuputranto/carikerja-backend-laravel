<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateProfile extends Model
{
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
        'last_education',
        'about_me',
        'photo_url',
        'cv_url',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
