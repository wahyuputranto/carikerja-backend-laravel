<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEducation extends Model
{
    protected $table = 'candidate_educations';
    
    protected $fillable = [
        'candidate_id',
        'institution_name',
        'degree',
        'major',
        'start_year',
        'end_year',
        'is_current',
        'gpa',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'gpa' => 'decimal:2',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
