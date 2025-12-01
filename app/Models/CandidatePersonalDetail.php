<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatePersonalDetail extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'fathers_name',
        'mothers_name',
        'height',
        'weight',
        'marital_status',
        'citizenship',
        'religion',
        'computer_skills',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
