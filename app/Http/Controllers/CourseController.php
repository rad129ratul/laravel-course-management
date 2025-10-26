<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Exception;

class CourseController extends Controller
{
    protected $courseRepository;
    protected $fileService;

    public function __construct(
        CourseRepositoryInterface $courseRepository,
        FileUploadService $fileService
    ) {
        $this->courseRepository = $courseRepository;
        $this->fileService = $fileService;
    }

    public function index()
    {
        try {
            $courses = $this->courseRepository->getAllCourses();
            return view('courses.index', compact('courses'));
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load courses: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $categories = ['Programming', 'Design', 'Business', 'Marketing', 'Other'];
        return view('courses.create', compact('categories'));
    }

    public function store(StoreCourseRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('feature_video')) {
                $data['feature_video_path'] = $this->fileService->uploadVideo(
                    $request->file('feature_video'),
                    'videos/features'
                );
            }

            if (isset($data['modules'])) {
                foreach ($data['modules'] as $moduleIndex => &$module) {
                    if (isset($module['contents'])) {
                        foreach ($module['contents'] as $contentIndex => &$content) {
                            
                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.video_file")) {
                                $content['video_path'] = $this->fileService->uploadVideo(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.video_file"),
                                    'videos/contents'
                                );
                                $content['type'] = 'video';
                            }

                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.image_file")) {
                                $content['image_path'] = $this->fileService->uploadImage(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.image_file"),
                                    'images/contents'
                                );
                                $content['type'] = $content['type'] ?? 'image';
                            }

                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.document_file")) {
                                $content['document_path'] = $this->fileService->uploadDocument(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.document_file"),
                                    'documents/contents'
                                );
                                $content['type'] = $content['type'] ?? 'document';
                            }

                            if (empty($content['type'])) {
                                if (!empty($content['video_url'])) {
                                    $content['type'] = 'video';
                                } elseif (!empty($content['content_text'])) {
                                    $content['type'] = 'text';
                                } else {
                                    $content['type'] = 'text';
                                }
                            }
                        }
                    }
                }
            }

            $course = $this->courseRepository->createCourse($data);

            return redirect()
                ->route('courses.show', $course->id)
                ->with('success', 'Course created successfully!');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create course: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $course = $this->courseRepository->getCourseWithRelations($id);
            return view('courses.show', compact('course'));
        } catch (Exception $e) {
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found.');
        }
    }

    public function edit($id)
    {
        try {
            $course = $this->courseRepository->getCourseWithRelations($id);
            $categories = ['Programming', 'Design', 'Business', 'Marketing', 'Other'];
            return view('courses.edit', compact('course', 'categories'));
        } catch (Exception $e) {
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found.');
        }
    }

    public function update(StoreCourseRequest $request, $id)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('feature_video')) {
                $course = $this->courseRepository->getCourseWithRelations($id);
                
                if ($course->feature_video_path) {
                    $this->fileService->deleteFile($course->feature_video_path);
                }
                
                $data['feature_video_path'] = $this->fileService->uploadVideo(
                    $request->file('feature_video'),
                    'videos/features'
                );
            }

            if (isset($data['modules'])) {
                foreach ($data['modules'] as $moduleIndex => &$module) {
                    if (isset($module['contents'])) {
                        foreach ($module['contents'] as $contentIndex => &$content) {
                            
                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.video_file")) {
                                $content['video_path'] = $this->fileService->uploadVideo(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.video_file"),
                                    'videos/contents'
                                );
                                $content['type'] = 'video';
                            }

                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.image_file")) {
                                $content['image_path'] = $this->fileService->uploadImage(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.image_file"),
                                    'images/contents'
                                );
                                $content['type'] = $content['type'] ?? 'image';
                            }

                            if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.document_file")) {
                                $content['document_path'] = $this->fileService->uploadDocument(
                                    $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.document_file"),
                                    'documents/contents'
                                );
                                $content['type'] = $content['type'] ?? 'document';
                            }

                            if (empty($content['type'])) {
                                $content['type'] = !empty($content['video_url']) ? 'video' : 'text';
                            }
                        }
                    }
                }
            }

            $course = $this->courseRepository->updateCourse($id, $data);

            return redirect()
                ->route('courses.show', $course->id)
                ->with('success', 'Course updated successfully!');

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update course: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->courseRepository->deleteCourse($id);
            
            return redirect()
                ->route('courses.index')
                ->with('success', 'Course deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete course: ' . $e->getMessage());
        }
    }
}