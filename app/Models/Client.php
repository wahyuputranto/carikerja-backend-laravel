<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Client extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'industry',
        'address',
        'phone',
        'website',
        'pic_name',
        'pic_phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($client) {
            if (empty($client->id)) {
                $client->id = (string) Str::uuid();
            }
        });
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'client_profile_id'); // We didn't rename the FK column in jobs table
    }

    public function batches()
    {
        return $this->hasMany(ClientBatch::class);
    }
}
