<nav class="navbar navbar-dark bg-dark-custom mb-4">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="{{ route('courses.index') }}">
            <i class="fas fa-graduation-cap me-2"></i>
            {{ config('app.name') }}
        </a>
        <div>
            <a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-light me-2">
                <i class="fas fa-list me-1"></i> Courses
            </a>
            <a href="{{ route('courses.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Create Course
            </a>
        </div>
    </div>
</nav>