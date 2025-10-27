<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::redirect('/', '/courses');

Route::resource('courses', CourseController::class);

Route::get('/test-db', function() {
    try {
        DB::connection()->getPdo();
        $tables = DB::select('SHOW TABLES');
        
        return response()->json([
            'status' => 'connected',
            'database' => DB::connection()->getDatabaseName(),
            'tables' => array_map(function($table) {
                return $table->{'Tables_in_'.env('DB_DATABASE')};
            }, $tables),
            'courses_count' => DB::table('courses')->count(),
            'modules_count' => DB::table('modules')->count(),
            'contents_count' => DB::table('contents')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'env_db_connection' => env('DB_CONNECTION'),
            'env_db_host' => env('DB_HOST'),
            'env_db_database' => env('DB_DATABASE')
        ]);
    }
});