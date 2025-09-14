<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'title',
        'description',
        'type',
        'required',
        'points',
        'weight',
        'display_order',
        'metadata'
    ];

    protected $casts = [
        'required' => 'boolean',
        'points' => 'integer',
        'metadata' => 'array',
        'weight' => 'float',
    ];

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class)->orderBy('display_order');
    }
}


