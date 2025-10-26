@extends('layouts.app')

@section('title', 'Courses')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page-header">Courses</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Course
        </a>
    </div>
</div>

@if($courses->count() > 0)
    <div class="row">
        @foreach($courses as $course)
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title text-white mb-3">{{ $course->title }}</h5>
                    <p class="card-text text-truncate">{{ Str::limit($course->description, 100) }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $course->category }}</span>
                        <span class="badge bg-secondary">
                            <i class="fas fa-book me-1"></i>{{ $course->modules_count }} Modules
                        </span>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('courses.show', $course->id) }}" class="btn btn-sm btn-primary flex-fill">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        <i class="fas fa-calendar me-1"></i>{{ $course->created_at->diffForHumans() }}
                    </small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $courses->links() }}
    </div>
@else
    <div class="card text-center py-5">
        <div class="card-body">
            <i class="fas fa-graduation-cap fa-4x text-muted mb-4"></i>
            <h3 class="text-white mb-3">No Courses Yet</h3>
            <p class="text-muted mb-4">Get started by creating your first course.</p>
            <a href="{{ route('courses.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Your First Course
            </a>
        </div>
    </div>
@endif
@endsection