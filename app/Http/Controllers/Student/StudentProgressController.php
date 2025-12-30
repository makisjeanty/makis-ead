<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentProgressService;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentProgressController extends Controller
{
    protected $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    /**
     * Get user's progress overview
     */
    public function overview(): JsonResponse
    {
        $user = auth()->user();
        $progressOverview = $this->progressService->getUserProgressOverview($user);

        return response()->json($progressOverview);
    }

    /**
     * Get detailed progress for a specific course
     */
    public function courseProgress($courseId): JsonResponse
    {
        $user = auth()->user();
        $course = Course::findOrFail($courseId);

        // Verify user is enrolled in the course
        if (!$this->progressService->isUserEnrolled($user, $course)) {
            return response()->json(['error' => 'User not enrolled in this course'], 403);
        }

        $courseProgress = $this->progressService->getCourseProgress($user, $course);

        return response()->json($courseProgress);
    }

    /**
     * Mark a lesson as completed
     */
    public function markLessonCompleted(Request $request, $lessonId): JsonResponse
    {
        $user = auth()->user();
        $lesson = Lesson::findOrFail($lessonId);

        // Check if user has access to this lesson
        $course = $lesson->module->course;
        if (!$this->progressService->isUserEnrolled($user, $course)) {
            return response()->json(['error' => 'User not enrolled in this course'], 403);
        }

        $this->progressService->updateLessonProgress($user, $lesson, $course);

        return response()->json(['success' => true]);
    }

    /**
     * Get progress for all enrolled courses
     */
    public function allCoursesProgress(): JsonResponse
    {
        $user = auth()->user();
        $progressOverview = $this->progressService->getUserProgressOverview($user);

        return response()->json([
            'courses' => $progressOverview['courses'],
            'total_courses' => $progressOverview['total_courses'],
            'average_progress' => $progressOverview['average_progress']
        ]);
    }
}