<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = [
        'candidate_id',
        'type',
        'title',
        'message',
        'url',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
