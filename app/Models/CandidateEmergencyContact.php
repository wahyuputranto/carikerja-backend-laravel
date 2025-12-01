<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateEmergencyContact extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'name',
        'contact_number',
        'relation',
        'address',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
