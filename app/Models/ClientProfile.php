<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClientProfile extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'company_name',
        'industry',
        'address',
        'phone',
        'website',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($profile) {
            if (empty($profile->id)) {
                $profile->id = (string) Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
