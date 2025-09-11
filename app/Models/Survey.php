<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'type', 'is_published', 'metadata',
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
}
