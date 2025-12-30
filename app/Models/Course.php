<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'long_description',
        'image',
        'price',
        'level',
        'duration_hours',
        'instructor_name',
        'instructor_bio',
        'instructor_avatar',
        'rating',
        'students_count',
        'is_published',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    // Relationships
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
    
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Module::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'enrollments')
            ->withTimestamps()
            ->withPivot('progress_percentage', 'completed_at', 'enrolled_at');
    }

    // Helper methods
    
    public function isFree()
    {
        return $this->price == 0;
    }
    
    public function isPublished()
    {
        return $this->is_published === true;
    }
    
    public function isPaid()
    {
        return $this->price > 0;
    }

    public function getEnrolledStudentsCountAttribute()
    {
        return $this->enrollments()->count();
    }
}
