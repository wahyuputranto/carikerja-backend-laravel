<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateCv extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'candidate_id',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'status',
        'rejection_note',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'file_size' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
