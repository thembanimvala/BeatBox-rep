<?php

namespace Database\Factories;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(rand(2,4));
        $slug = Str::slug($name);

        return [
            'writer_id' => rand(1,5),
            'name' => $name,
            'slug' => $slug,
            'intro' => fake()->paragraph(),
            'content' => fake()->text(900),
            'photo' => fake()->imageurl,
        ];
    }
}
