<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow all users for now
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents' => 'nullable|array',
            'modules.*.contents.*.title' => 'nullable|string|max:255',
            'modules.*.contents.*.type' => 'nullable|string|in:video,text,image,document',
            'modules.*.contents.*.content_text' => 'nullable|string',
            'modules.*.contents.*.video_url' => 'nullable|url|max:500',
            'modules.*.contents.*.video_source_type' => 'nullable|string|in:youtube,vimeo,upload',
            'modules.*.contents.*.video_length' => 'nullable|string|max:50',
            'modules.*.contents.*.video_file' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200',
            'modules.*.contents.*.image_file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'modules.*.contents.*.document_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'modules.*.contents.*.column_position' => 'nullable|string|max:50',
        ];

        // Feature video validation - required for create, optional for update
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['feature_video'] = 'nullable|file|mimes:mp4,mov,avi,wmv,flv|max:51200';
        } else {
            $rules['feature_video'] = 'required|file|mimes:mp4,mov,avi,wmv,flv|max:51200';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a course title.',
            'title.max' => 'Course title cannot exceed 255 characters.',
            'description.required' => 'Please provide a course description.',
            'category.required' => 'Please select a course category.',
            
            // Feature video messages
            'feature_video.required' => 'Please upload a feature video for the course.',
            'feature_video.file' => 'Feature video must be a valid file.',
            'feature_video.mimes' => 'Feature video must be a video file (mp4, mov, avi, wmv, flv).',
            'feature_video.max' => 'Feature video size cannot exceed 50MB (51200KB).',
            
            // Module messages
            'modules.required' => 'At least one module is required.',
            'modules.min' => 'Please add at least one module to the course.',
            'modules.*.title.required' => 'Module title is required.',
            'modules.*.title.max' => 'Module title cannot exceed 255 characters.',
            
            // Content messages
            'modules.*.contents.*.title.max' => 'Content title cannot exceed 255 characters.',
            'modules.*.contents.*.type.in' => 'Content type must be video, text, image, or document.',
            'modules.*.contents.*.video_url.url' => 'Please enter a valid video URL.',
            'modules.*.contents.*.video_url.max' => 'Video URL is too long.',
            'modules.*.contents.*.video_source_type.in' => 'Video source must be YouTube, Vimeo, or Upload.',
            
            // File upload messages
            'modules.*.contents.*.video_file.file' => 'Video must be a valid file.',
            'modules.*.contents.*.video_file.mimes' => 'Video file must be mp4, mov, avi, or wmv format.',
            'modules.*.contents.*.video_file.max' => 'Video file cannot exceed 50MB.',
            'modules.*.contents.*.image_file.file' => 'Image must be a valid file.',
            'modules.*.contents.*.image_file.mimes' => 'Image must be jpg, jpeg, png, gif, or webp format.',
            'modules.*.contents.*.image_file.max' => 'Image file cannot exceed 2MB.',
            'modules.*.contents.*.document_file.file' => 'Document must be a valid file.',
            'modules.*.contents.*.document_file.mimes' => 'Document must be pdf, doc, docx, ppt, or pptx format.',
            'modules.*.contents.*.document_file.max' => 'Document file cannot exceed 10MB.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'course title',
            'description' => 'course description',
            'category' => 'course category',
            'feature_video' => 'feature video',
            'modules.*.title' => 'module title',
            'modules.*.contents.*.title' => 'content title',
            'modules.*.contents.*.video_url' => 'video URL',
            'modules.*.contents.*.video_file' => 'video file',
            'modules.*.contents.*.image_file' => 'image file',
            'modules.*.contents.*.document_file' => 'document file',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Clean up modules data - remove empty contents
        if ($this->has('modules')) {
            $modules = $this->modules;
            
            foreach ($modules as $key => $module) {
                if (isset($module['contents'])) {
                    // Filter out completely empty contents
                    $modules[$key]['contents'] = array_filter($module['contents'], function ($content) {
                        return !empty($content['title']) || 
                               !empty($content['content_text']) || 
                               !empty($content['video_url']) ||
                               $this->hasFile("modules.{$key}.contents.*.video_file") ||
                               $this->hasFile("modules.{$key}.contents.*.image_file") ||
                               $this->hasFile("modules.{$key}.contents.*.document_file");
                    });
                }
            }
            
            $this->merge(['modules' => $modules]);
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Log validation errors for debugging
        Log::warning('Course validation failed', [
            'errors' => $validator->errors()->toArray(),
            'has_feature_video' => $this->hasFile('feature_video'),
            'feature_video_error' => $this->hasFile('feature_video') ? $this->file('feature_video')->getError() : null,
            'request_method' => $this->method(),
            'request_url' => $this->url(),
        ]);

        parent::failedValidation($validator);
    }
}