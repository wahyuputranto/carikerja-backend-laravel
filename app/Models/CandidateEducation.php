<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CandidateEducation extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'candidate_educations';
    
    protected $fillable = [
        'candidate_id',
        'institution_name',
        'degree',
        'major',
        'start_year',
        'end_year',
        'is_current',
        'gpa',
    ];

    protected $casts = [
        'is_current' => 'boolean',
        'gpa' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($education) {
            if (empty($education->id)) {
                $education->id = (string) Str::uuid();
            }
        });
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }
}
