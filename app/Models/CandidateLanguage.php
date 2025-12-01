<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateLanguage extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'language',
        'speaking',
        'reading',
        'writing',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
