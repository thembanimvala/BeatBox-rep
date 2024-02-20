<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::truncate();

        $count = 1;

        while ($count < 20) {
            $blog = Blog::factory()->create();
            $number = rand(2,5);
            $tags = Tag::pluck('id')->random($number);
            $blog->tags()->sync($tags);
            $count ++;
        }


    }
}
