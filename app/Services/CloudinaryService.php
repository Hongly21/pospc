<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Transformation;
use Illuminate\Http\UploadedFile;

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
        // Extract public ID from URL like: https://res.cloudinary.com/cloud_name/image/upload/v123/folder/filename
        preg_match('/\/([a-zA-Z0-9_-]+\/[a-zA-Z0-9_.-]+)(\.\w+)?$/i', $url, $matches);
        return $matches[1] ?? null;
    }
}
