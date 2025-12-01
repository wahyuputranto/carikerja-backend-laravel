<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidatePassport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'passport_number',
        'issue_date',
        'issued_by',
        'expiry_date',
        'file_path',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
