<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Curso>
 */
class CursoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'slug' => fake()->slug(),
            'description' => fake()->sentence(10),
            'long_description' => fake()->paragraphs(3, true),
            'image' => fake()->imageUrl(640, 480, 'education', true),
            'price' => fake()->randomFloat(2, 29.90, 299.90),
            'level' => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
            'duration_hours' => fake()->numberBetween(5, 100),
            'instructor_name' => fake()->name(),
            'instructor_bio' => fake()->paragraph(),
            'instructor_avatar' => fake()->imageUrl(200, 200, 'people', true),
            'rating' => fake()->randomFloat(2, 3.5, 5.0),
            'students_count' => fake()->numberBetween(0, 1000),
            'is_published' => true,
        ];
    }
}
