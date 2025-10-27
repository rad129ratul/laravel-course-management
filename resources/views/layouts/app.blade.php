<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Course Management')</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    @if(app()->environment('production'))
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @else
        @vite(['resources/css/app.css'])
    @endif
</head>
<body class="bg-dark text-light">
    @include('components.navbar')
    <main class="container-fluid px-4 py-4">
        @include('components.alerts')
        @yield('content')
    </main>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    @if(app()->environment('production'))
        <script src="{{ asset('js/app.js') }}"></script>
    @else
        @vite(['resources/js/app.js'])
    @endif
    
    @stack('scripts')
</body>
</html>