<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id', 'respondent_id', 'score'
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function respondent()
    {
        return $this->belongsTo(Respondent::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}


