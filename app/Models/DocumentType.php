<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;
    protected $table = 'master_document_types';
    protected $guarded = [];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'chunkable' => 'boolean',
        'allowed_mimetypes' => 'array',
        'max_size' => 'integer',
    ];
}
