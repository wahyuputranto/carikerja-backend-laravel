<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';
    protected $guarded = [];
    protected $keyType = 'string';
    public $incrementing = false;

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
