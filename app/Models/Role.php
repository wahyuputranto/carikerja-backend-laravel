<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'slug', 'description'];

    public function privileges()
    {
        return $this->hasMany(RolePrivilege::class);
    }
}
