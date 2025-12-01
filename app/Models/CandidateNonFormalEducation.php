<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateNonFormalEducation extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'candidate_non_formal_educations';

    protected $fillable = [
        'candidate_id',
        'year',
        'name',
        'subject',
        'country',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
