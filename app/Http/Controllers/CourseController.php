<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Repositories\Contracts\CourseRepositoryInterface;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        } catch (QueryException $e) {
            Log::error('Database error loading courses: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Database error occurred. Please try again later.');
        } catch (Exception $e) {
            Log::error('Error loading courses: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Failed to load courses. Please try again.');
        }
    }

    public function create()
    {
        try {
            $categories = ['Programming', 'Design', 'Business', 'Marketing', 'Other'];
            return view('courses.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->route('courses.index')->with('error', 'Failed to load course creation form.');
        }
    }

    public function store(StoreCourseRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();

            // Enhanced feature video upload with detailed logging
            if ($request->hasFile('feature_video')) {
                $file = $request->file('feature_video');
                
                // Detailed logging for debugging
                Log::info('Feature video upload attempt', [
                    'original_name' => $file->getClientOriginalName(),
                    'size_bytes' => $file->getSize(),
                    'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                    'mime_type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension(),
                    'error_code' => $file->getError(),
                    'is_valid' => $file->isValid(),
                    'tmp_path' => $file->getRealPath(),
                    'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                    'sys_temp_dir' => sys_get_temp_dir(),
                    'temp_writable' => is_writable(sys_get_temp_dir()),
                ]);
                
                // Check for upload errors
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    $errorMessages = [
                        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                        UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                        UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload',
                    ];
                    
                    $errorMsg = $errorMessages[$file->getError()] ?? 'Unknown upload error';
                    
                    Log::error('Feature video upload error', [
                        'error_code' => $file->getError(),
                        'error_message' => $errorMsg
                    ]);
                    
                    throw new Exception("Feature video upload failed: {$errorMsg}");
                }
                
                // Check if file is valid
                if (!$file->isValid()) {
                    Log::error('Feature video file is invalid');
                    throw new Exception('Feature video file is invalid or corrupted');
                }
                
                try {
                    $data['feature_video_path'] = $this->fileService->uploadVideo(
                        $file,
                        'videos/features'
                    );
                    
                    Log::info('Feature video uploaded successfully', [
                        'path' => $data['feature_video_path']
                    ]);
                } catch (Exception $e) {
                    Log::error('Feature video upload service failed', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new Exception('Failed to save feature video: ' . $e->getMessage());
                }
            }

            // Process module contents with file uploads
            if (isset($data['modules'])) {
                foreach ($data['modules'] as $moduleIndex => &$module) {
                    if (isset($module['contents'])) {
                        foreach ($module['contents'] as $contentIndex => &$content) {
                            
                            try {
                                // Video file upload
                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.video_file")) {
                                    $videoFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.video_file");
                                    
                                    if ($videoFile->getError() !== UPLOAD_ERR_OK) {
                                        Log::warning("Content video upload error for module {$moduleIndex}, content {$contentIndex}", [
                                            'error_code' => $videoFile->getError()
                                        ]);
                                        continue; // Skip this file but continue processing
                                    }
                                    
                                    $content['video_path'] = $this->fileService->uploadVideo(
                                        $videoFile,
                                        'videos/contents'
                                    );
                                    $content['type'] = 'video';
                                }

                                // Image file upload
                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.image_file")) {
                                    $imageFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.image_file");
                                    
                                    if ($imageFile->getError() !== UPLOAD_ERR_OK) {
                                        Log::warning("Content image upload error for module {$moduleIndex}, content {$contentIndex}", [
                                            'error_code' => $imageFile->getError()
                                        ]);
                                        continue;
                                    }
                                    
                                    $content['image_path'] = $this->fileService->uploadImage(
                                        $imageFile,
                                        'images/contents'
                                    );
                                    $content['type'] = $content['type'] ?? 'image';
                                }

                                // Document file upload
                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.document_file")) {
                                    $docFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.document_file");
                                    
                                    if ($docFile->getError() !== UPLOAD_ERR_OK) {
                                        Log::warning("Content document upload error for module {$moduleIndex}, content {$contentIndex}", [
                                            'error_code' => $docFile->getError()
                                        ]);
                                        continue;
                                    }
                                    
                                    $content['document_path'] = $this->fileService->uploadDocument(
                                        $docFile,
                                        'documents/contents'
                                    );
                                    $content['type'] = $content['type'] ?? 'document';
                                }
                            } catch (Exception $e) {
                                Log::error("Content file upload failed for module {$moduleIndex}, content {$contentIndex}", [
                                    'message' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                                throw new Exception("Failed to upload file in Module " . ($moduleIndex + 1) . ", Content " . ($contentIndex + 1) . ": " . $e->getMessage());
                            }

                            // Set default content type
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

            // Create course with all data
            $course = $this->courseRepository->createCourse($data);

            DB::commit();

            Log::info('Course created successfully', [
                'course_id' => $course->id,
                'title' => $course->title
            ]);

            return redirect()
                ->route('courses.show', $course->id)
                ->with('success', 'Course created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed during course creation', [
                'errors' => $e->errors()
            ]);
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Please fix the validation errors and try again.');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Database error creating course: ' . $e->getMessage(), [
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->with('error', 'Database error occurred. Please try again.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error creating course: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $course = $this->courseRepository->getCourseWithRelations($id);
            return view('courses.show', compact('course'));
        } catch (QueryException $e) {
            Log::error("Database error loading course {$id}: " . $e->getMessage());
            return redirect()
                ->route('courses.index')
                ->with('error', 'Database error occurred while loading the course.');
        } catch (Exception $e) {
            Log::error("Error loading course {$id}: " . $e->getMessage());
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found or failed to load.');
        }
    }

    public function edit($id)
    {
        try {
            $course = $this->courseRepository->getCourseWithRelations($id);
            $categories = ['Programming', 'Design', 'Business', 'Marketing', 'Other'];
            return view('courses.edit', compact('course', 'categories'));
        } catch (Exception $e) {
            Log::error("Error loading edit form for course {$id}: " . $e->getMessage());
            return redirect()
                ->route('courses.index')
                ->with('error', 'Course not found or failed to load edit form.');
        }
    }

    public function update(StoreCourseRequest $request, $id)
    {
        DB::beginTransaction();
        
        try {
            $data = $request->validated();

            if ($request->hasFile('feature_video')) {
                $file = $request->file('feature_video');
                
                if ($file->getError() !== UPLOAD_ERR_OK || !$file->isValid()) {
                    throw new Exception('Invalid feature video file');
                }
                
                try {
                    $course = $this->courseRepository->getCourseWithRelations($id);
                    
                    if ($course->feature_video_path) {
                        $this->fileService->deleteFile($course->feature_video_path);
                    }
                    
                    $data['feature_video_path'] = $this->fileService->uploadVideo(
                        $file,
                        'videos/features'
                    );
                } catch (Exception $e) {
                    Log::error('Feature video upload failed during update: ' . $e->getMessage());
                    throw new Exception('Failed to upload new feature video.');
                }
            }

            if (isset($data['modules'])) {
                foreach ($data['modules'] as $moduleIndex => &$module) {
                    if (isset($module['contents'])) {
                        foreach ($module['contents'] as $contentIndex => &$content) {
                            
                            try {
                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.video_file")) {
                                    $videoFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.video_file");
                                    
                                    if ($videoFile->getError() === UPLOAD_ERR_OK && $videoFile->isValid()) {
                                        $content['video_path'] = $this->fileService->uploadVideo(
                                            $videoFile,
                                            'videos/contents'
                                        );
                                        $content['type'] = 'video';
                                    }
                                }

                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.image_file")) {
                                    $imageFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.image_file");
                                    
                                    if ($imageFile->getError() === UPLOAD_ERR_OK && $imageFile->isValid()) {
                                        $content['image_path'] = $this->fileService->uploadImage(
                                            $imageFile,
                                            'images/contents'
                                        );
                                        $content['type'] = $content['type'] ?? 'image';
                                    }
                                }

                                if ($request->hasFile("modules.{$moduleIndex}.contents.{$contentIndex}.document_file")) {
                                    $docFile = $request->file("modules.{$moduleIndex}.contents.{$contentIndex}.document_file");
                                    
                                    if ($docFile->getError() === UPLOAD_ERR_OK && $docFile->isValid()) {
                                        $content['document_path'] = $this->fileService->uploadDocument(
                                            $docFile,
                                            'documents/contents'
                                        );
                                        $content['type'] = $content['type'] ?? 'document';
                                    }
                                }
                            } catch (Exception $e) {
                                Log::error("Content file upload failed during update: " . $e->getMessage());
                                throw new Exception("Failed to upload file in Module " . ($moduleIndex + 1));
                            }

                            if (empty($content['type'])) {
                                $content['type'] = !empty($content['video_url']) ? 'video' : 'text';
                            }
                        }
                    }
                }
            }

            $course = $this->courseRepository->updateCourse($id, $data);

            DB::commit();

            Log::info('Course updated successfully', ['course_id' => $course->id]);

            return redirect()
                ->route('courses.show', $course->id)
                ->with('success', 'Course updated successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            Log::warning('Validation failed during course update', [
                'course_id' => $id,
                'errors' => $e->errors()
            ]);
            return back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', 'Please fix the validation errors and try again.');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Database error updating course {$id}: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Database error occurred while updating.');
                
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error updating course {$id}: " . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $course = $this->courseRepository->getCourseWithRelations($id);
            
            if ($course->feature_video_path) {
                $this->fileService->deleteFile($course->feature_video_path);
            }
            
            foreach ($course->modules as $module) {
                foreach ($module->contents as $content) {
                    if ($content->video_path) {
                        $this->fileService->deleteFile($content->video_path);
                    }
                    if ($content->image_path) {
                        $this->fileService->deleteFile($content->image_path);
                    }
                    if ($content->document_path) {
                        $this->fileService->deleteFile($content->document_path);
                    }
                }
            }
            
            $this->courseRepository->deleteCourse($id);
            
            DB::commit();
            
            Log::info('Course deleted successfully', ['course_id' => $id]);
            
            return redirect()
                ->route('courses.index')
                ->with('success', 'Course deleted successfully!');
                
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error("Database error deleting course {$id}: " . $e->getMessage());
            return back()->with('error', 'Database error occurred while deleting the course.');
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error deleting course {$id}: " . $e->getMessage());
            return back()->with('error', 'Failed to delete course. Please try again.');
        }
    }
}