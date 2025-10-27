<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckFileUpload
{
    /**
     * Handle an incoming request and check for file upload errors.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only check on POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $files = $request->allFiles();
            
            foreach ($this->flattenFiles($files) as $key => $file) {
                if (is_object($file) && method_exists($file, 'getError')) {
                    $errorCode = $file->getError();
                    
                    if ($errorCode !== UPLOAD_ERR_OK) {
                        $error = $this->getUploadErrorMessage($errorCode);
                        
                        // Log the error for debugging
                        Log::error('File upload error detected', [
                            'file_key' => $key,
                            'error_code' => $errorCode,
                            'error_message' => $error,
                            'file_name' => $file->getClientOriginalName(),
                            'file_size' => $file->getSize(),
                            'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                            'sys_temp_dir' => sys_get_temp_dir(),
                        ]);
                        
                        return back()
                            ->withInput($request->except(['feature_video', 'modules']))
                            ->with('error', "File upload failed: {$error}. Please try a smaller file or contact support.");
                    }
                }
            }
        }

        return $next($request);
    }

    /**
     * Flatten nested file arrays
     *
     * @param array $files
     * @return array
     */
    private function flattenFiles(array $files, $prefix = ''): array
    {
        $result = [];
        
        foreach ($files as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenFiles($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }
        
        return $result;
    }

    /**
     * Get human-readable error message for upload error code
     *
     * @param int $errorCode
     * @return string
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds server upload limit (upload_max_filesize in php.ini)',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds form upload limit (MAX_FILE_SIZE directive)',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded (connection interrupted)',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory on server',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk (permission denied)',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload',
        ];

        return $errors[$errorCode] ?? "Unknown upload error (Code: {$errorCode})";
    }
}