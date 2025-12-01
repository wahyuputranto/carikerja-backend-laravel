<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasUuids;

    protected $fillable = [
        'application_id',
        'candidate_id', // Added
        'interviewer_id',
        'stage',
        'scheduled_at',
        'type',
        'meeting_link',
        'result',
        'feedback_notes',
        'location_address',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }
}
