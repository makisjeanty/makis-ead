<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Course;
use App\Services\StudentProgressService;

class DashboardController extends Controller
{
    protected $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }
    /**
     * Show student dashboard
     */
    public function index()
    {
        $user = auth()->user();

        // Get detailed progress overview using the new service
        $progressOverview = $this->progressService->getUserProgressOverview($user);

        // Get recent activity
        $recentEnrollments = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->latest('enrolled_at')
            ->take(5)
            ->get();

        // Get recommended courses (not enrolled)
        $enrolledCourseIds = $user->courses()->pluck('course_id')->toArray();
        $recommendedCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->orderBy('students_count', 'desc')
            ->limit(4)
            ->get();

        // Prepare stats for the view
        $stats = [
            'total_courses' => Enrollment::where('user_id', $user->id)->count(),
            'completed_courses' => Enrollment::where('user_id', $user->id)->whereNotNull('completed_at')->count(),
            'in_progress' => Enrollment::where('user_id', $user->id)->whereNull('completed_at')->count(),
            'certificates' => Enrollment::where('user_id', $user->id)->whereNotNull('certificate_issued_at')->count(),
        ];

        // pass `enrollments` for compatibility with the view
        $enrollments = $recentEnrollments;

        return view('student.dashboard', compact(
            'progressOverview',
            'recentEnrollments',
            'recommendedCourses',
            'stats',
            'enrollments'
        ));
    }
}
