<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache; // Importante: Importar o Cache

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        // 1. PERFORMANCE: Cria uma chave única para esta busca específica
        // Ex: courses_page_1_search_marketing_level_iniciante
        $cacheKey = 'courses_v1_' . md5(json_encode($request->all()));

        // 2. PERFORMANCE: Lembra desta consulta por 10 minutos (600 segundos)
        $courses = Cache::remember($cacheKey, 600, function () use ($request) {
            
            // Lógica de consulta (Só roda se não estiver em cache)
            $query = Course::where('is_published', true);

            if ($request->has('search') && $request->search != '') {
                $query->where('title', 'like', "%{$request->search}%");
            }

            if ($request->has('level')) {
                $query->whereIn('level', $request->level);
            }

            if ($request->has('price')) {
                if ($request->price == 'free') {
                    $query->where('price', 0);
                } elseif ($request->price == 'paid') {
                    $query->where('price', '>', 0);
                }
            }

            // Eager Loading da imagem não é necessário pois é URL string, 
            // mas usamos paginate para limitar a carga.
            return $query->latest()->paginate(12);
        });

        return view('student.courses.index', compact('courses'));
    }

    public function watch($courseSlug, $lessonId = null)
    {
        // Cache por usuário para evitar expor conteúdo de alunos diferentes.
        $userId = auth()->id();
        $courseCacheKey = "course_details_{$courseSlug}_user_{$userId}";

        $course = Cache::remember($courseCacheKey, 3600, function () use ($courseSlug) {
            return Course::with(['modules.lessons' => function($query) {
                $query->orderBy('sort_order', 'asc');
            }])->where('slug', $courseSlug)->firstOrFail();
        });

        // Authorization: ensure user can view this course (free or enrolled)
        $this->authorize('view', $course);

        if (!$lessonId) {
            $firstModule = $course->modules->sortBy('sort_order')->first();
            if ($firstModule && $firstModule->lessons->count() > 0) {
                $currentLesson = $firstModule->lessons->sortBy('sort_order')->first();
            } else {
                return redirect()->back()->with('error', 'Curso sem aulas.');
            }
        } else {
            // Cache da lição individual por usuário
            $lessonCacheKey = "lesson_{$lessonId}_user_{$userId}";
            $currentLesson = Cache::remember($lessonCacheKey, 3600, function () use ($lessonId) {
                return Lesson::findOrFail($lessonId);
            });
        }

        return view('student.classroom.watch', compact('course', 'currentLesson'));
    }
}