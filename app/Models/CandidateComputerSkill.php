<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CandidateComputerSkill extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'skill_name',
        'proficiency_level',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
