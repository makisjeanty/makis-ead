<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $courses = Course::where('is_published', true)->get();
        $categories = Category::all();

        return response()->view('sitemap', compact('courses', 'categories'))
            ->header('Content-Type', 'text/xml');
    }
}
