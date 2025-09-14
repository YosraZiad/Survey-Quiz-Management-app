<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'is_published', 'metadata', 'survey_number',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'metadata' => 'array',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('display_order');
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($survey) {
            if (empty($survey->survey_number)) {
                $survey->survey_number = static::max('survey_number') + 1;
            }
        });
    }
}
