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

    public function test_enrolled_user_can_access_course_watch(): void
    {
        $user = User::factory()->create();

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

        $response = $this->actingAs($user)->get(route('student.classroom.watch', ['slug' => $course->slug]));

        $response->assertOk();
    }

    public function test_non_enrolled_user_cannot_access_paid_course(): void
    {
        $user = User::factory()->create();

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
