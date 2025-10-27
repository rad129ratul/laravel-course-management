<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::redirect('/', '/courses');

Route::resource('courses', CourseController::class);