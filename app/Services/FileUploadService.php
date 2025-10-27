<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class FileUploadService
{
    public function uploadVideo(UploadedFile $file, string $directory = 'videos'): string
    {
        try {
            $this->validateFile($file, ['mp4', 'mov', 'avi', 'wmv', 'flv'], 51200);
            return $this->storeFile($file, $directory);
        } catch (Exception $e) {
            Log::error('Video upload failed in service', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'size' => $file->getSize()
            ]);
            throw new Exception("Video upload failed: " . $e->getMessage());
        }
    }

    public function uploadImage(UploadedFile $file, string $directory = 'images'): string
    {
        try {
            $this->validateFile($file, ['jpg', 'jpeg', 'png', 'gif', 'webp'], 2048);
            return $this->storeFile($file, $directory);
        } catch (Exception $e) {
            Log::error('Image upload failed in service', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw new Exception("Image upload failed: " . $e->getMessage());
        }
    }

    public function uploadDocument(UploadedFile $file, string $directory = 'documents'): string
    {
        try {
            $this->validateFile($file, ['pdf', 'doc', 'docx', 'ppt', 'pptx'], 10240);
            return $this->storeFile($file, $directory);
        } catch (Exception $e) {
            Log::error('Document upload failed in service', [
                'message' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw new Exception("Document upload failed: " . $e->getMessage());
        }
    }

    public function deleteFile(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                $deleted = Storage::disk('public')->delete($path);
                
                if ($deleted) {
                    Log::info('File deleted successfully', ['path' => $path]);
                }
                
                return $deleted;
            }
            
            Log::warning('Attempted to delete non-existent file', ['path' => $path]);
            return false;
        } catch (Exception $e) {
            Log::error('File deletion failed', [
                'path' => $path,
                'message' => $e->getMessage()
            ]);
            throw new Exception("File deletion failed: " . $e->getMessage());
        }
    }

    private function validateFile(UploadedFile $file, array $allowedExtensions, int $maxSizeKB): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type '{$extension}'. Allowed: " . implode(', ', $allowedExtensions));
        }

        // file size check
        $fileSizeKB = $file->getSize() / 1024;
        $fileSizeMB = round($fileSizeKB / 1024, 2);
        
        if ($fileSizeKB > $maxSizeKB) {
            $maxSizeMB = round($maxSizeKB / 1024, 2);
            throw new Exception("File size ({$fileSizeMB}MB) exceeds limit of {$maxSizeMB}MB");
        }

        // file uploaded check
        if (!$file->isValid()) {
            throw new Exception("File is invalid or was not properly uploaded");
        }
    }

    private function storeFile(UploadedFile $file, string $directory): string
    {
        try {
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(40) . '_' . time() . '.' . $extension;
            
            Log::info('Attempting to store file', [
                'filename' => $filename,
                'directory' => $directory,
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'disk' => 'public',
                'storage_path' => storage_path('app/public'),
                'storage_writable' => is_writable(storage_path('app/public')),
            ]);
            
            // directory exists check
            $fullPath = storage_path('app/public/' . $directory);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
                Log::info('Created directory', ['path' => $fullPath]);
            }
            
            // Store
            $path = $file->storeAs($directory, $filename, 'public');
            
            if (!$path) {
                throw new Exception("Failed to store file - storeAs returned false");
            }

            // Verify stored
            if (!Storage::disk('public')->exists($path)) {
                throw new Exception("File was not found after storage attempt");
            }

            Log::info('File stored successfully', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path),
                'exists' => file_exists(storage_path('app/public/' . $path)),
            ]);

            return $path;
            
        } catch (Exception $e) {
            Log::error('File storage failed', [
                'directory' => $directory,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception("Failed to store file: " . $e->getMessage());
        }
    }

    public function getFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        try {
            return Storage::disk('public')->url($path);
        } catch (Exception $e) {
            Log::error('Failed to generate file URL', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function fileExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            return Storage::disk('public')->exists($path);
        } catch (Exception $e) {
            Log::error('Failed to check file existence', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function getFileSize(?string $path): ?int
    {
        if (!$path || !$this->fileExists($path)) {
            return null;
        }

        try {
            return (int) (Storage::disk('public')->size($path) / 1024);
        } catch (Exception $e) {
            Log::error('Failed to get file size', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}