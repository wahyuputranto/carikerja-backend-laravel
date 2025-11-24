<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MasterDocumentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_mandatory',
        'allowed_mimetypes',
        'chunkable',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'chunkable' => 'boolean',
        'allowed_mimetypes' => 'array',
    ];
}
