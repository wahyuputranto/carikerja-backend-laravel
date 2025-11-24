<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CandidateDocument extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'candidate_id',
        'document_type_id',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
