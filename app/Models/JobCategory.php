<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobCategory extends Model
{
    protected $table = 'master_job_categories';
    protected $fillable = ['name', 'slug'];
}
