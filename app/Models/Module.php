<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Um Módulo pertence a um Curso
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Um Módulo tem muitas Aulas
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
