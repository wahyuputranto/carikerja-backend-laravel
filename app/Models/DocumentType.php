<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    use SoftDeletes;
    protected $table = 'master_document_types';
    protected $guarded = [];

    public const ALLOWED_MIMETYPES = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'video/mp4',
    ];

    protected $casts = [
        'is_mandatory' => 'boolean',
        'chunkable' => 'boolean',
        'allowed_mimetypes' => 'array',
        'max_size' => 'integer',
    ];

    public function getTemplateUrlAttribute() {
        if (!$this->template) return null;
        if (str_starts_with($this->template, 'http')) return $this->template;
        return \Illuminate\Support\Facades\Storage::disk('minio')->url($this->template);
    }

    protected $appends = ['template_url'];
}
