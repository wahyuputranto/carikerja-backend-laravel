<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateExperience extends Model
{
    protected $table = 'candidate_experiences';
    
    protected $fillable = [
        'candidate_id',
        'company_name',
        'position',
        'start_date',
        'end_date',
        'is_current',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
