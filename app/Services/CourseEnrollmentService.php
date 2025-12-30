<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CourseEnrollmentService
{
    /**
     * Enroll a user in a course
     */
    public function enroll(User $user, Course $course): Enrollment
    {
        if (!$course->isPublished()) {
            throw new \Exception('Cannot enroll in an unpublished course');
        }

        if ($user->courses()->where('course_id', $course->id)->exists()) {
            throw new \Exception('User is already enrolled in this course');
        }

        return DB::transaction(function () use ($user, $course) {
            $enrollment = Enrollment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);

            // Increment the students count on the course
            $course->increment('students_count');

            Log::info("User enrolled in course", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrollment_id' => $enrollment->id
            ]);

            return $enrollment;
        });
    }

    /**
     * Check if user is enrolled in a course
     */
    public function isEnrolled(User $user, Course $course): bool
    {
        return $user->courses()->where('course_id', $course->id)->exists();
    }

    /**
     * Process enrollment after a successful payment
     */
    public function processEnrollmentFromOrder(Order $order): ?Enrollment
    {
        $courseId = $order->metadata['course_id'] ?? null;
        
        if (!$courseId) {
            Log::warning("No course_id found in order metadata", ['order_id' => $order->id]);
            return null;
        }

        $course = Course::find($courseId);
        if (!$course) {
            Log::error("Course not found for enrollment", [
                'course_id' => $courseId,
                'order_id' => $order->id
            ]);
            return null;
        }

        $user = $order->user;
        if (!$user) {
            Log::error("User not found for order", ['order_id' => $order->id]);
            return null;
        }

        try {
            return $this->enroll($user, $course);
        } catch (\Exception $e) {
            Log::error("Failed to create enrollment from order", [
                'order_id' => $order->id,
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Get user's enrollment for a specific course
     */
    public function getUserEnrollment(User $user, Course $course): ?Enrollment
    {
        return $user->enrollments()->where('course_id', $course->id)->first();
    }

    /**
     * Update progress for a user in a course
     */
    public function updateProgress(Enrollment $enrollment, int $progress): void
    {
        if ($progress < 0) {
            $progress = 0;
        } elseif ($progress > 100) {
            $progress = 100;
        }

        $enrollment->update(['progress_percentage' => $progress]);

        Log::info("Updated enrollment progress", [
            'enrollment_id' => $enrollment->id,
            'progress' => $progress
        ]);
    }

    /**
     * Unenroll a user from a course
     */
    public function unenroll(User $user, Course $course): bool
    {
        return DB::transaction(function () use ($user, $course) {
            $enrollment = $this->getUserEnrollment($user, $course);
            if (!$enrollment) {
                return false;
            }

            $result = $enrollment->delete();
            
            // Decrement the students count on the course
            $course->decrement('students_count');

            Log::info("User unenrolled from course", [
                'user_id' => $user->id,
                'course_id' => $course->id
            ]);

            return $result;
        });
    }
}