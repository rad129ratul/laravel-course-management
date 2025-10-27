<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\Content;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create a sample course
        $course = Course::create([
            'title' => 'Introduction to Laravel',
            'description' => 'Learn the fundamentals of Laravel framework including routing, controllers, views, and Eloquent ORM.',
            'category' => 'Web Development',
        ]);

        // Create modules for the course
        $module1 = Module::create([
            'course_id' => $course->id,
            'title' => 'Getting Started with Laravel',
            'order' => 1,
        ]);

        $module2 = Module::create([
            'course_id' => $course->id,
            'title' => 'Database and Eloquent ORM',
            'order' => 2,
        ]);

        $module3 = Module::create([
            'course_id' => $course->id,
            'title' => 'Building Your First Application',
            'order' => 3,
        ]);

        // Create contents for module 1
        Content::create([
            'module_id' => $module1->id,
            'title' => 'What is Laravel?',
            'type' => 'text',
            'content_text' => 'Laravel is a PHP web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling.',
            'order' => 1,
        ]);

        Content::create([
            'module_id' => $module1->id,
            'title' => 'Installation Guide',
            'type' => 'text',
            'content_text' => 'Learn how to install Laravel using Composer and set up your development environment.',
            'order' => 2,
        ]);

        // Create contents for module 2
        Content::create([
            'module_id' => $module2->id,
            'title' => 'Eloquent ORM Introduction',
            'type' => 'text',
            'content_text' => 'Eloquent is Laravel\'s built-in ORM that provides a beautiful, simple ActiveRecord implementation for working with your database.',
            'order' => 1,
        ]);

        Content::create([
            'module_id' => $module2->id,
            'title' => 'Database Migrations',
            'type' => 'text',
            'content_text' => 'Migrations are like version control for your database, allowing your team to modify and share the application\'s database schema.',
            'order' => 2,
        ]);

        // Create contents for module 3
        Content::create([
            'module_id' => $module3->id,
            'title' => 'Building a Blog Application',
            'type' => 'text',
            'content_text' => 'In this section, we\'ll build a complete blog application with posts, categories, and comments.',
            'order' => 1,
        ]);

        $this->command->info('Sample course data created successfully!');
        $this->command->info('Course: ' . $course->title);
        $this->command->info('Modules: 3');
        $this->command->info('Contents: 5');
    }
}