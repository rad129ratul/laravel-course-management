@extends('layouts.app')

@section('title', 'View Course')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page-header">{{ $course->title }}</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('courses.index') }}" class="btn btn-outline-light me-2">
            <i class="fas fa-arrow-left me-2"></i>Back to Courses
        </a>
        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">Course Information</div>
    <div class="card-body">
        <div class="mb-3">
            <span class="badge bg-primary">{{ $course->category }}</span>
            <span class="badge bg-secondary ms-2">
                <i class="fas fa-book me-1"></i>{{ $course->modules->count() }} Modules
            </span>
            <span class="badge bg-secondary ms-2">
                <i class="fas fa-calendar me-1"></i>{{ $course->created_at->format('M d, Y') }}
            </span>
        </div>
        
        <div class="mb-4">
            <h5 class="text-white mb-2">Description</h5>
            <p class="text-muted">{{ $course->description }}</p>
        </div>

        @if($course->feature_video_path)
        <div class="mb-3">
            <h5 class="text-white mb-3">Feature Video</h5>
            <video controls class="w-100" style="max-height: 500px; border-radius: 0.375rem;">
                <source src="{{ asset('storage/' . $course->feature_video_path) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
        @endif
    </div>
</div>

@if($course->modules->count() > 0)
<div class="card">
    <div class="card-header">Course Modules</div>
    <div class="card-body">
        <div class="accordion" id="modulesAccordion">
            @foreach($course->modules as $index => $module)
            <div class="accordion-item mb-3" style="background-color: #374151; border: 1px solid #4b5563; border-radius: 0.375rem;">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#module{{ $module->id }}" style="background-color: #374151; color: #ffffff; border: none;">
                        <strong>Module {{ $index + 1 }}: {{ $module->title }}</strong>
                        <span class="badge bg-secondary ms-2">{{ $module->contents->count() }} Contents</span>
                    </button>
                </h2>
                <div id="module{{ $module->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#modulesAccordion">
                    <div class="accordion-body" style="background-color: #2d3748;">
                        @if($module->contents->count() > 0)
                            @foreach($module->contents as $contentIndex => $content)
                            <div class="content-item mb-4 p-3" style="background-color: #374151; border-left: 3px solid #0d6efd; border-radius: 0.375rem;">
                                <h6 class="text-white mb-2">
                                    <i class="fas fa-play-circle text-primary me-2"></i>
                                    {{ $content->title ?: 'Content ' . ($contentIndex + 1) }}
                                </h6>
                                
                                @if($content->type)
                                <span class="badge bg-info mb-2">{{ ucfirst($content->type) }}</span>
                                @endif

                                @if($content->video_url)
                                <div class="mb-3">
                                    <label class="text-muted small">Video URL:</label>
                                    <div>
                                        <a href="{{ $content->video_url }}" target="_blank" class="text-primary">
                                            {{ $content->video_url }}
                                        </a>
                                        @if($content->video_source_type)
                                        <span class="badge bg-secondary ms-2">{{ ucfirst($content->video_source_type) }}</span>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                @if($content->video_path)
                                <div class="mb-3">
                                    <label class="text-muted small mb-2">Uploaded Video:</label>
                                    <video controls class="w-100" style="max-height: 400px; border-radius: 0.375rem;">
                                        <source src="{{ asset('storage/' . $content->video_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                                @endif

                                @if($content->video_length)
                                <div class="mb-2">
                                    <label class="text-muted small">Duration:</label>
                                    <span class="text-white">{{ $content->video_length }}</span>
                                </div>
                                @endif

                                @if($content->content_text)
                                <div class="mb-3">
                                    <label class="text-muted small">Content:</label>
                                    <p class="text-white mb-0">{{ $content->content_text }}</p>
                                </div>
                                @endif

                                @if($content->image_path)
                                <div class="mb-3">
                                    <label class="text-muted small mb-2">Image:</label>
                                    <img src="{{ asset('storage/' . $content->image_path) }}" alt="Content Image" class="img-fluid" style="max-height: 300px; border-radius: 0.375rem;">
                                </div>
                                @endif

                                @if($content->document_path)
                                <div class="mb-2">
                                    <label class="text-muted small">Document:</label>
                                    <a href="{{ asset('storage/' . $content->document_path) }}" target="_blank" class="btn btn-sm btn-outline-light">
                                        <i class="fas fa-file-download me-1"></i>Download Document
                                    </a>
                                </div>
                                @endif

                                @if($content->column_position)
                                <div class="mb-2">
                                    <label class="text-muted small">Position:</label>
                                    <span class="badge bg-secondary">{{ ucfirst($content->column_position) }}</span>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No contents available for this module.</p>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@else
<div class="card text-center py-5">
    <div class="card-body">
        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
        <p class="text-muted">No modules available for this course.</p>
    </div>
</div>
@endif
@endsection

@push('scripts')
<style>
.accordion-button:not(.collapsed) {
    background-color: #374151 !important;
    color: #ffffff !important;
}

.accordion-button::after {
    filter: invert(1);
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.content-item:last-child {
    margin-bottom: 0 !important;
}
</style>
@endpush