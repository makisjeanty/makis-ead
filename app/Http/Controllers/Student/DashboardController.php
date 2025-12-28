<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;

class DashboardController extends Controller
{
    /**
     * Show student dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get enrolled courses with progress
        $enrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->latest('enrolled_at')
            ->get();

        // Calculate statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'completed_courses' => $enrollments->where('completed_at', '!=', null)->count(),
            'in_progress' => $enrollments->where('completed_at', null)->count(),
            'certificates' => $enrollments->where('certificate_issued_at', '!=', null)->count(),
            'average_progress' => $enrollments->avg('progress_percentage') ?? 0,
        ];

        // Get recent activity
        $recentEnrollments = $enrollments->take(5);

        // Get recommended courses (not enrolled)
        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        $recommendedCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->orderBy('students_count', 'desc')
            ->limit(4)
            ->get();

        return view('student.dashboard', compact('enrollments', 'stats', 'recentEnrollments', 'recommendedCourses'));
    }
}
