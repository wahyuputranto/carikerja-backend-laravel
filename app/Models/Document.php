<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'candidate_documents';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function application()
    {
        return $this->belongsTo(JobApplication::class, 'application_id');
    }
}
