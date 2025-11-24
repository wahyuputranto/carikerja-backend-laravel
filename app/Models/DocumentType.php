<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'master_document_types';
    protected $guarded = [];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'chunkable' => 'boolean',
        'allowed_mimetypes' => 'array',
    ];
}
