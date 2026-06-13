<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Transformation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            ]
        ]);
    }

    /**
     * Check if using Cloudinary in current environment
     *
     * @return bool
     */
    private function shouldUseCloudinary(): bool
    {
        // Check if all Cloudinary credentials are available
        $hasCloudinaryCredentials = env('CLOUDINARY_API_KEY') &&
                                   env('CLOUDINARY_API_SECRET') &&
                                   env('CLOUDINARY_CLOUD_NAME');

        if (!$hasCloudinaryCredentials) {
            return false;
        }

        // If in production, use Cloudinary
        if (env('APP_ENV') === 'production') {
            return true;
        }

        // If FILESYSTEM_DISK is explicitly set to cloudinary
        if (env('FILESYSTEM_DISK') === 'cloudinary') {
            return true;
        }

        return false;
    }

    /**
     * Upload a file with intelligent storage selection
     * - Local storage for local/testing environments
     * - Cloudinary for production/hosting environments
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $publicId
     * @return string|null The secure URL/path of the uploaded file
     */
    public function uploadWithFallback(UploadedFile $file, string $folder = 'products', ?string $publicId = null): ?string
    {
        // In production with Cloudinary configured, try Cloudinary first
        if ($this->shouldUseCloudinary()) {
            $result = $this->upload($file, $folder, $publicId);
            if ($result !== null) {
                return $result;
            }
            \Log::warning('Cloudinary upload failed for ' . $file->getClientOriginalName());
        }

        // For local/testing or as fallback, use local storage
        \Log::info('Using local storage for upload: ' . $file->getClientOriginalName());
        return $this->uploadToLocal($file, $folder);
    }

    /**
     * Upload a file to local storage
     *
     * @param UploadedFile $file
     * @param string $folder
     * @return string|null The relative path of the uploaded file
     */
    private function uploadToLocal(UploadedFile $file, string $folder = 'products'): ?string
    {
        try {
            $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = "uploads/{$folder}";

            $savedPath = Storage::disk('public')->putFileAs(
                $path,
                $file,
                $filename
            );

            // Return the accessible URL
            return Storage::disk('public')->url($savedPath);
        } catch (\Exception $e) {
            \Log::error('Local storage upload error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Upload a file to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string|null $publicId
     * @return string|null The secure URL of the uploaded file
     */
    public function upload(UploadedFile $file, string $folder = 'products', ?string $publicId = null): ?string
    {
        try {
            $options = [
                'folder' => $folder,
                'resource_type' => 'auto',
            ];

            if ($publicId) {
                $options['public_id'] = $publicId;
            }

            $result = $this->cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                $options
            );

            return $result['secure_url'] ?? null;
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from Cloudinary
     *
     * @param string $publicId
     * @return bool
     */
    public function delete(string $publicId): bool
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            return $result['result'] === 'ok';
        } catch (\Exception $e) {
            \Log::error('Cloudinary delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract public ID from a Cloudinary URL
     *
     * @param string $url
     * @return string|null
     */
    public function getPublicIdFromUrl(string $url): ?string
    {
        // Check if this is a Cloudinary URL
        if (strpos($url, 'cloudinary.com') === false && strpos($url, 'res.cloudinary.com') === false) {
            return null; // Not a Cloudinary URL
        }

        // Extract public ID from URL like: https://res.cloudinary.com/cloud_name/image/upload/v123/folder/filename
        preg_match('/\/([a-zA-Z0-9_-]+\/[a-zA-Z0-9_.-]+)(\.\w+)?$/i', $url, $matches);
        return $matches[1] ?? null;
    }

    /**
     * Extract local file path from a local storage URL
     *
     * @param string $url
     * @return string|null The relative path in storage/app/public
     */
    public function getLocalFilePathFromUrl(string $url): ?string
    {
        // Check if this is a local storage URL like: /storage/uploads/products/...
        if (strpos($url, '/storage/') === false) {
            return null;
        }

        // Extract path after /storage/
        preg_match('/\/storage\/(.+)$/i', $url, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Delete a local file from storage
     *
     * @param string $filePath The relative path in storage/app/public
     * @return bool
     */
    public function deleteLocalFile(string $filePath): bool
    {
        try {
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
                \Log::info('Local file deleted: ' . $filePath);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            \Log::error('Local file deletion error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Intelligently delete a file (Cloudinary or Local)
     *
     * @param string $fileUrl The URL or path of the file to delete
     * @return bool
     */
    public function deleteFile(string $fileUrl): bool
    {
        // Try to delete as Cloudinary file
        $publicId = $this->getPublicIdFromUrl($fileUrl);
        if ($publicId) {
            return $this->delete($publicId);
        }

        // Try to delete as local file
        $localPath = $this->getLocalFilePathFromUrl($fileUrl);
        if ($localPath) {
            return $this->deleteLocalFile($localPath);
        }

        return false;
    }
}
