<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/courses');
Route::resource('courses', CourseController::class);