{{-- resources/views/courses/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Course')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page-header">Edit Course</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-outline-light">
            <i class="fas fa-arrow-left me-2"></i>Back to Course
        </a>
    </div>
</div>

<form action="{{ route('courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" id="courseForm">
    @csrf
    @method('PUT')
    
    <div class="card mb-4">
        <div class="card-header">Course Details</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Course Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title', $course->title) }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Course Description *</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" required>{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category" class="form-label">Category *</label>
                <select class="form-select @error('category') is-invalid @enderror" 
                        id="category" name="category" required>
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category', $course->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="feature_video" class="form-label">Feature Video (Leave empty to keep current)</label>
                @if($course->feature_video_path)
                    <div class="mb-2">
                        <small class="text-muted">Current video uploaded</small>
                    </div>
                @endif
                <input type="file" class="form-control @error('feature_video') is-invalid @enderror" 
                       id="feature_video" name="feature_video" accept="video/*">
                <small class="text-muted">Max size: 50MB. Formats: mp4, mov, avi, wmv, flv</small>
                @error('feature_video')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div id="modules-container">
        @foreach($course->modules as $moduleIndex => $module)
        <div class="module-container" data-module-index="{{ $moduleIndex }}">
            <div class="module-header">
                <h4 class="module-title">Module {{ $moduleIndex + 1 }}</h4>
                <button type="button" class="btn btn-danger btn-sm delete-btn delete-module" style="{{ $loop->first && $course->modules->count() == 1 ? 'display:none;' : '' }}">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">Module Title *</label>
                <input type="text" class="form-control" 
                       name="modules[{{ $moduleIndex }}][title]" value="{{ old("modules.$moduleIndex.title", $module->title) }}" required>
            </div>

            <div class="contents-container" data-module-index="{{ $moduleIndex }}">
                @foreach($module->contents as $contentIndex => $content)
                <div class="content-container" data-content-index="{{ $contentIndex }}">
                    <button type="button" class="btn btn-danger btn-sm delete-btn delete-content">
                        <i class="fas fa-times"></i>
                    </button>

                    <div class="mb-3">
                        <label class="form-label">Content Title</label>
                        <input type="text" class="form-control" 
                               name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][title]" 
                               value="{{ old("modules.$moduleIndex.contents.$contentIndex.title", $content->title) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video Source Type</label>
                            <select class="form-select video-source-type" 
                                    name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_source_type]">
                                <option value="">Select Source</option>
                                <option value="youtube" {{ old("modules.$moduleIndex.contents.$contentIndex.video_source_type", $content->video_source_type) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old("modules.$moduleIndex.contents.$contentIndex.video_source_type", $content->video_source_type) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="upload" {{ old("modules.$moduleIndex.contents.$contentIndex.video_source_type", $content->video_source_type) == 'upload' ? 'selected' : '' }}>Upload Video</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video Length</label>
                            <input type="text" class="form-control" 
                                   name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_length]" 
                                   value="{{ old("modules.$moduleIndex.contents.$contentIndex.video_length", $content->video_length) }}" 
                                   placeholder="e.g., 10:30">
                        </div>
                    </div>

                    <div class="mb-3 video-url-container">
                        <label class="form-label">Video URL</label>
                        <input type="url" class="form-control" 
                               name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_url]" 
                               value="{{ old("modules.$moduleIndex.contents.$contentIndex.video_url", $content->video_url) }}" 
                               placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <div class="mb-3 video-upload-container" style="display:none;">
                        <label class="form-label">Upload Video</label>
                        @if($content->video_path)
                            <div class="mb-2">
                                <small class="text-muted">Current video uploaded</small>
                            </div>
                        @endif
                        <input type="file" class="form-control" 
                               name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][video_file]" accept="video/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content Text</label>
                        <textarea class="form-control" 
                                  name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][content_text]" 
                                  rows="3">{{ old("modules.$moduleIndex.contents.$contentIndex.content_text", $content->content_text) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image (Optional)</label>
                        @if($content->image_path)
                            <div class="mb-2">
                                <small class="text-muted">Current image uploaded</small>
                            </div>
                        @endif
                        <input type="file" class="form-control" 
                               name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][image_file]" accept="image/*">
                        <small class="text-muted">Max size: 2MB</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Column Position</label>
                        <select class="form-select" 
                                name="modules[{{ $moduleIndex }}][contents][{{ $contentIndex }}][column_position]">
                            <option value="">Select Column</option>
                            <option value="left" {{ old("modules.$moduleIndex.contents.$contentIndex.column_position", $content->column_position) == 'left' ? 'selected' : '' }}>Left</option>
                            <option value="right" {{ old("modules.$moduleIndex.contents.$contentIndex.column_position", $content->column_position) == 'right' ? 'selected' : '' }}>Right</option>
                            <option value="full" {{ old("modules.$moduleIndex.contents.$contentIndex.column_position", $content->column_position) == 'full' ? 'selected' : '' }}>Full Width</option>
                        </select>
                    </div>
                </div>
                @endforeach
            </div>

            <button type="button" class="btn add-btn add-content mb-3">
                <i class="fas fa-plus me-2"></i>Add Content +
            </button>
        </div>
        @endforeach
    </div>

    <button type="button" class="btn add-btn add-module mb-4" id="addModuleBtn">
        <i class="fas fa-plus me-2"></i>Add Module +
    </button>

    <div class="d-flex gap-3 mb-4">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Update Course
        </button>
        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-danger">
            <i class="fas fa-times me-2"></i>Cancel
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/course-form.js') }}"></script>
<script>
$(document).ready(function() {
    moduleCounter = {{ $course->modules->count() }};
    contentCounters = {!! json_encode($course->modules->mapWithKeys(function($module, $index) {
        return [$index => $module->contents->count()];
    })) !!};
});
</script>
@endpush