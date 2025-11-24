<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RolePrivilege extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['role_id', 'permission'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
