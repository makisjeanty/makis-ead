<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'progress_percentage',
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress_percentage' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
    
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }
    
    public function scopeInProgress($query)
    {
        return $query->whereNull('completed_at')
            ->where('progress_percentage', '>', 0);
    }
}
