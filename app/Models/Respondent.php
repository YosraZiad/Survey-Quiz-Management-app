<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respondent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','email','gender','age','education','location'
    ];

    public function responses()
    {
        return $this->hasMany(Response::class);
    }
}


