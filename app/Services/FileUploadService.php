<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
            throw new Exception("Video upload failed: " . $e->getMessage());
        }
    }

    public function uploadImage(UploadedFile $file, string $directory = 'images'): string
    {
        try {
            $this->validateFile($file, ['jpg', 'jpeg', 'png', 'gif', 'webp'], 2048);
            return $this->storeFile($file, $directory);
        } catch (Exception $e) {
            throw new Exception("Image upload failed: " . $e->getMessage());
        }
    }

    public function uploadDocument(UploadedFile $file, string $directory = 'documents'): string
    {
        try {
            $this->validateFile($file, ['pdf', 'doc', 'docx', 'ppt', 'pptx'], 10240);
            return $this->storeFile($file, $directory);
        } catch (Exception $e) {
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
                return Storage::disk('public')->delete($path);
            }
            return false;
        } catch (Exception $e) {
            throw new Exception("File deletion failed: " . $e->getMessage());
        }
    }

    private function validateFile(UploadedFile $file, array $allowedExtensions, int $maxSizeKB): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception("Invalid file type. Allowed: " . implode(', ', $allowedExtensions));
        }

        $fileSizeKB = $file->getSize() / 1024;
        if ($fileSizeKB > $maxSizeKB) {
            throw new Exception("File size exceeds limit of {$maxSizeKB}KB");
        }
    }

    private function storeFile(UploadedFile $file, string $directory): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '_' . time() . '.' . $extension;
        
        $path = $file->storeAs($directory, $filename, 'public');
        
        if (!$path) {
            throw new Exception("Failed to store file");
        }

        return $path;
    }

    public function getFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    public function fileExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }

    public function getFileSize(?string $path): ?int
    {
        if (!$path || !$this->fileExists($path)) {
            return null;
        }

        return (int) (Storage::disk('public')->size($path) / 1024);
    }
}