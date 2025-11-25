<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'signed_at' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'check_date' => 'date',
        'visa_expiry' => 'date',
        'departure_at' => 'datetime',
        'arrival_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}
