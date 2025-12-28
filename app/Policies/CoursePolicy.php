<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class CoursePolicy
{
    /**
     * Determine whether the user can view the course content (lessons).
     * Free courses are viewable by everyone; paid courses require enrollment.
     */
    public function view(User $user, Course $course): bool
    {
        if ($course->price == 0) {
            return true;
        }

        // Admin bypass (if user has is_admin flag)
        if (property_exists($user, 'is_admin') && $user->is_admin) {
            return true;
        }

        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }
}
