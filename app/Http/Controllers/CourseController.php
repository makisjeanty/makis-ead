<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Cache key based on request parameters
        $cacheKey = 'courses_' . md5(json_encode($request->all()));

        $data = Cache::remember($cacheKey, 60 * 5, function () use ($request) {
            $query = Course::with("category")->where("is_published", true);

            // Search filter
            if ($request->has("search") && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where("title", "like", "%{$search}%")
                      ->orWhere("description", "like", "%{$search}%")
                      ->orWhere("long_description", "like", "%{$search}%");
                });
            }

            // Category filter
            if ($request->has("category") && $request->category) {
                $query->where("category_id", $request->category);
            }

            // Level filter
            if ($request->has("level") && $request->level) {
                $query->where("level", $request->level);
            }

            // Price filter
            if ($request->has("price_filter")) {
                if ($request->price_filter === 'free') {
                    $query->where("price", 0);
                } elseif ($request->price_filter === 'paid') {
                    $query->where("price", ">", 0);
                }
            }

            // Sorting
            $sort = $request->get("sort", "popular");
            switch ($sort) {
                case "rating":
                    $query->orderBy("rating", "desc");
                    break;
                case "price_asc":
                    $query->orderBy("price", "asc");
                    break;
                case "price_desc":
                    $query->orderBy("price", "desc");
                    break;
                case "newest":
                    $query->orderBy("created_at", "desc");
                    break;
                default:
                    $query->orderBy("students_count", "desc");
            }

            // Paginate results
            return [
                'courses' => $query->paginate(12)->withQueryString(),
                'categories' => Category::withCount('courses')->get()
            ];
        });

        return view("courses.index", [
            'courses' => $data['courses'],
            'categories' => $data['categories']
        ]);
    }

    public function show($slug)
    {
        $cacheKey = 'course_details_' . $slug;

        $course = Cache::remember($cacheKey, 60 * 30, function () use ($slug) {
            return Course::with([
                "category", 
                "modules" => function($query) {
                    $query->orderBy('sort_order', 'asc');
                },
                "modules.lessons" => function($query) {
                    $query->orderBy('sort_order', 'asc');
                }
            ])
            ->where("slug", $slug)
            ->where("is_published", true)
            ->firstOrFail();
        });

        return view("courses.show", compact("course"));
    }
}
