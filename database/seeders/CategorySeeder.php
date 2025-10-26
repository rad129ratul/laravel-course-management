<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::create([
            'title' => 'Introduction to Laravel',
            'description' => 'Learn Laravel from scratch',
            'category' => 'Programming'
        ]);

        $module1 = $course->modules()->create([
            'title' => 'Getting Started',
            'order' => 0
        ]);

        $module1->contents()->create([
            'title' => 'Installation Guide',
            'type' => 'text',
            'content_text' => 'Learn how to install Laravel',
            'order' => 0
        ]);

        $module2 = $course->modules()->create([
            'title' => 'Advanced Topics',
            'order' => 1
        ]);

        $module2->contents()->create([
            'title' => 'Routing Basics',
            'type' => 'video',
            'video_url' => 'https://youtube.com/watch?v=example',
            'video_source_type' => 'youtube',
            'video_length' => '15:30',
            'order' => 0
        ]);
    }
}