@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page-header">Create a Course</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-light">
            <i class="fas fa-arrow-left me-2"></i>Back to Course Page
        </a>
    </div>
</div>

<form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data" id="courseForm">
    @csrf
    
    <div class="card mb-4">
        <div class="card-header">Course Details</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="title" class="form-label">Course Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                       id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Course Description *</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
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
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="feature_video" class="form-label">Feature Video *</label>
                <input type="file" class="form-control @error('feature_video') is-invalid @enderror" 
                       id="feature_video" name="feature_video" accept="video/*" required>
                <small class="text-muted">Max size: 50MB. Formats: mp4, mov, avi, wmv, flv</small>
                @error('feature_video')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div id="modules-container">
        <div class="module-container" data-module-index="0">
            <div class="module-header">
                <h4 class="module-title">Module 1</h4>
                <button type="button" class="btn btn-danger btn-sm delete-btn delete-module" style="display:none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label">Module Title *</label>
                <input type="text" class="form-control" name="modules[0][title]" required>
            </div>

            <div class="contents-container" data-module-index="0">
                <div class="content-container" data-content-index="0">
                    <button type="button" class="btn btn-danger btn-sm delete-btn delete-content">
                        <i class="fas fa-times"></i>
                    </button>

                    <div class="mb-3">
                        <label class="form-label">Content Title</label>
                        <input type="text" class="form-control" name="modules[0][contents][0][title]">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video Source Type</label>
                            <select class="form-select video-source-type" name="modules[0][contents][0][video_source_type]">
                                <option value="">Select Source</option>
                                <option value="youtube">YouTube</option>
                                <option value="vimeo">Vimeo</option>
                                <option value="upload">Upload Video</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Video Length</label>
                            <input type="text" class="form-control" name="modules[0][contents][0][video_length]" placeholder="e.g., 10:30">
                        </div>
                    </div>

                    <div class="mb-3 video-url-container">
                        <label class="form-label">Video URL</label>
                        <input type="url" class="form-control" name="modules[0][contents][0][video_url]" placeholder="https://youtube.com/watch?v=...">
                    </div>

                    <div class="mb-3 video-upload-container" style="display:none;">
                        <label class="form-label">Upload Video</label>
                        <input type="file" class="form-control" name="modules[0][contents][0][video_file]" accept="video/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content Text</label>
                        <textarea class="form-control" name="modules[0][contents][0][content_text]" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Image (Optional)</label>
                        <input type="file" class="form-control" name="modules[0][contents][0][image_file]" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Column Position</label>
                        <select class="form-select" name="modules[0][contents][0][column_position]">
                            <option value="">Select Column</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="full">Full Width</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" class="btn add-btn add-content mb-3">
                <i class="fas fa-plus me-2"></i>Add Content +
            </button>
        </div>
    </div>

    <button type="button" class="btn add-btn add-module mb-4" id="addModuleBtn">
        <i class="fas fa-plus me-2"></i>Add Module +
    </button>

    <div class="d-flex gap-3 mb-4">
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save me-2"></i>Save Course
        </button>
        <a href="{{ route('courses.index') }}" class="btn btn-danger">
            <i class="fas fa-times me-2"></i>Cancel
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/course-form.js') }}"></script>
@endpush