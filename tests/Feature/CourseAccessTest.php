<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Illuminate\Support\Facades\Route::any('/profile/edit', function () {
            return '';
        })->name('profile.edit');
    }

    public function test_enrolled_user_can_access_course_watch(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $course = Course::create([
            'title' => 'Paid Course',
            'slug' => 'paid-course',
            'price' => 99.90,
            'is_published' => true,
        ]);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        // Add a module and a lesson so the classroom has content
        $module = \App\Models\Module::create([
            'course_id' => $course->id,
            'title' => 'Intro',
            'sort_order' => 1,
        ]);

        \App\Models\Lesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1',
            'sort_order' => 1,
            'content' => 'Lesson content',
        ]);

        $response = $this->actingAs($user)->get(route('student.classroom.watch', ['slug' => $course->slug]));

        $response->assertOk();
    }

    public function test_non_enrolled_user_cannot_access_paid_course(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $course = Course::create([
            'title' => 'Paid Course',
            'slug' => 'paid-course-2',
            'price' => 49.90,
            'is_published' => true,
        ]);

        $response = $this->actingAs($user)->get(route('student.classroom.watch', ['slug' => $course->slug]));

        $response->assertStatus(403);
    }
}
