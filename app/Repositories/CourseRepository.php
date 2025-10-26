<?php

namespace App\Repositories;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CourseRepository implements CourseRepositoryInterface
{
    protected $model;

    public function __construct(Course $model)
    {
        $this->model = $model;
    }

    public function getAllCourses()
    {
        try {
            return $this->model
                ->withCount('modules')
                ->latest()
                ->paginate(10);
        } catch (Exception $e) {
            Log::error('Error fetching courses: ' . $e->getMessage());
            throw $e;
        }
    }

    public function createCourse(array $data)
    {
        DB::beginTransaction();
        
        try {
            $course = $this->model->create([
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'feature_video_path' => $data['feature_video_path'] ?? null,
            ]);

            if (isset($data['modules']) && is_array($data['modules'])) {
                foreach ($data['modules'] as $index => $moduleData) {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'order' => $index,
                    ]);

                    if (isset($moduleData['contents']) && is_array($moduleData['contents'])) {
                        foreach ($moduleData['contents'] as $contentIndex => $contentData) {
                            $module->contents()->create([
                                'title' => $contentData['title'] ?? '',
                                'type' => $contentData['type'] ?? 'text',
                                'content_text' => $contentData['content_text'] ?? null,
                                'video_url' => $contentData['video_url'] ?? null,
                                'video_source_type' => $contentData['video_source_type'] ?? null,
                                'video_length' => $contentData['video_length'] ?? null,
                                'video_path' => $contentData['video_path'] ?? null,
                                'image_path' => $contentData['image_path'] ?? null,
                                'document_path' => $contentData['document_path'] ?? null,
                                'column_position' => $contentData['column_position'] ?? null,
                                'order' => $contentIndex,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return $course->load('modules.contents');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getCourseWithRelations($id)
    {
        try {
            return $this->model
                ->with(['modules.contents'])
                ->findOrFail($id);
        } catch (Exception $e) {
            Log::error('Error fetching course: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateCourse($id, array $data)
    {
        DB::beginTransaction();
        
        try {
            $course = $this->model->findOrFail($id);
            
            $course->update([
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'feature_video_path' => $data['feature_video_path'] ?? $course->feature_video_path,
            ]);

            $course->modules()->delete();

            if (isset($data['modules']) && is_array($data['modules'])) {
                foreach ($data['modules'] as $index => $moduleData) {
                    $module = $course->modules()->create([
                        'title' => $moduleData['title'],
                        'order' => $index,
                    ]);

                    if (isset($moduleData['contents']) && is_array($moduleData['contents'])) {
                        foreach ($moduleData['contents'] as $contentIndex => $contentData) {
                            $module->contents()->create([
                                'title' => $contentData['title'] ?? '',
                                'type' => $contentData['type'] ?? 'text',
                                'content_text' => $contentData['content_text'] ?? null,
                                'video_url' => $contentData['video_url'] ?? null,
                                'video_source_type' => $contentData['video_source_type'] ?? null,
                                'video_length' => $contentData['video_length'] ?? null,
                                'video_path' => $contentData['video_path'] ?? null,
                                'image_path' => $contentData['image_path'] ?? null,
                                'document_path' => $contentData['document_path'] ?? null,
                                'column_position' => $contentData['column_position'] ?? null,
                                'order' => $contentIndex,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return $course->load('modules.contents');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error updating course: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteCourse($id)
    {
        try {
            $course = $this->model->findOrFail($id);
            return $course->delete();
        } catch (Exception $e) {
            Log::error('Error deleting course: ' . $e->getMessage());
            throw $e;
        }
    }
}