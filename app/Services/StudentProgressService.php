<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use App\Models\LessonCompletion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentProgressService
{
    /**
     * Update progress for a student in a specific lesson
     * CRITICAL FIX: Only updates if enrollment exists (doesn't create automatically)
     */
    public function updateLessonProgress(User $user, Lesson $lesson, Course $course): void
    {
        DB::transaction(function () use ($user, $lesson, $course) {
            // CRITICAL FIX: Get enrollment, don't create if doesn't exist
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();
            
            if (!$enrollment) {
                Log::warning("Attempted to update progress without enrollment", [
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id
                ]);
                throw new \Exception('User must be enrolled in the course to track progress');
            }

            // Calculate the progress percentage based on completed lessons
            $totalLessons = $course->lessons()->count();
            if ($totalLessons == 0) {
                return; // Nothing to track
            }

            // Mark lesson as completed for this user
            $this->markLessonAsCompleted($user, $lesson);

            // Calculate new progress percentage
            $completedLessons = $this->getCompletedLessonsCount($user, $course);
            $progressPercentage = min(100, (int) (($completedLessons / $totalLessons) * 100));

            // Update enrollment progress
            $enrollment->update(['progress_percentage' => $progressPercentage]);

            // Check if course is completed
            if ($progressPercentage >= 100 && is_null($enrollment->completed_at)) {
                $enrollment->update(['completed_at' => now()]);
            }

            Log::info("Student progress updated", [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
                'progress_percentage' => $progressPercentage,
                'completed_lessons' => $completedLessons,
                'total_lessons' => $totalLessons
            ]);
        });
    }

    /**
     * Mark a lesson as completed for a user
     */
    protected function markLessonAsCompleted(User $user, Lesson $lesson): void
    {
        LessonCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ], [
            'completed_at' => now(),
        ]);
    }

    /**
     * Get the count of completed lessons for a user in a course
     */
    public function getCompletedLessonsCount(User $user, Course $course): int
    {
        return LessonCompletion::forUser($user->id)
            ->forCourse($course->id)
            ->count();
    }

    /**
     * Get detailed progress for a course
     */
    public function getCourseProgress(User $user, Course $course): array
    {
        $totalLessons = $course->lessons()->count();
        $completedLessons = $this->getCompletedLessonsCount($user, $course);
        $progressPercentage = $totalLessons > 0 ? min(100, (int) (($completedLessons / $totalLessons) * 100)) : 0;

        $modulesWithProgress = $course->modules->map(function ($module) use ($user) {
            $totalLessons = $module->lessons->count();
            $completedLessons = 0;
            
            foreach ($module->lessons as $lesson) {
                $lessonKey = "course_{$module->course_id}_lesson_{$lesson->id}";
                $completedLessons += in_array("course_{$module->course_id}_lesson_{$lesson->id}", $user->metadata['completed_lessons'] ?? []) ? 1 : 0;
            }
            
            $moduleProgress = $totalLessons > 0 ? min(100, (int) (($completedLessons / $totalLessons) * 100)) : 0;
            
            return [
                'module' => $module,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'progress_percentage' => $moduleProgress,
            ];
        });

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'progress_percentage' => $progressPercentage,
            'modules' => $modulesWithProgress,
        ];
    }

    /**
     * Get all enrolled courses with detailed progress for a user
     */
    public function getUserProgressOverview(User $user): array
    {
        $enrollments = Enrollment::with('course.modules.lessons')
            ->where('user_id', $user->id)
            ->get();

        $coursesProgress = [];
        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->where('completed_at', '!=', null)->count();
        $inProgressCourses = $enrollments->where('completed_at', null)->where('progress_percentage', '>', 0)->count();
        $notStartedCourses = $enrollments->where('progress_percentage', 0)->count();
        
        $averageProgress = $totalCourses > 0 ? $enrollments->avg('progress_percentage') : 0;

        foreach ($enrollments as $enrollment) {
            $courseProgress = $this->getCourseProgress($user, $enrollment->course);
            
            $coursesProgress[] = [
                'enrollment' => $enrollment,
                'progress' => $courseProgress,
            ];
        }

        return [
            'total_courses' => $totalCourses,
            'completed_courses' => $completedCourses,
            'in_progress_courses' => $inProgressCourses,
            'not_started_courses' => $notStartedCourses,
            'average_progress' => round($averageProgress, 2),
            'courses' => $coursesProgress,
        ];
    }

    /**
     * Check if a specific lesson is completed by the user
     */
    public function isLessonCompleted(User $user, Lesson $lesson): bool
    {
        return LessonCompletion::where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->exists();
    }

    /**
     * Check if a user is enrolled in a specific course
     */
    public function isUserEnrolled(User $user, Course $course): bool
    {
        return Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();
    }
}