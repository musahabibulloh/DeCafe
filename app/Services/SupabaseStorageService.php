<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    /**
     * Get Supabase configuration settings.
     */
    protected static function getConfig()
    {
        return [
            'url' => env('SUPABASE_URL'),
            'key' => env('SUPABASE_KEY'),
            'bucket' => env('SUPABASE_BUCKET', 'decafe'),
        ];
    }

    /**
     * Upload file to Supabase storage. Falls back to local public disk if config is missing.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @return string File URL or path
     */
    public static function upload($file, $folder = 'uploads')
    {
        $config = self::getConfig();

        if (empty($config['url']) || empty($config['key'])) {
            // Fallback to local disk
            return $file->store($folder, 'public');
        }

        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $path = $folder . '/' . $filename;
        
        $url = rtrim($config['url'], '/') . '/storage/v1/object/' . $config['bucket'] . '/' . $path;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $config['key'],
                'apikey' => $config['key'],
                'Content-Type' => $file->getMimeType(),
            ])->withBody($file->getContent(), $file->getMimeType())
              ->post($url);

            if ($response->successful()) {
                // Return full public URL
                return rtrim($config['url'], '/') . '/storage/v1/object/public/' . $config['bucket'] . '/' . $path;
            }

            Log::error('Supabase upload failed: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Supabase upload exception: ' . $e->getMessage());
        }

        // Fallback to local storage on failure
        return $file->store($folder, 'public');
    }

    /**
     * Delete file from Supabase storage or local storage.
     *
     * @param string $path
     * @return void
     */
    public static function delete($path)
    {
        if (empty($path)) {
            return;
        }

        // If it's a local storage path (does not start with HTTP)
        if (!Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
            return;
        }

        $config = self::getConfig();
        if (empty($config['url']) || empty($config['key'])) {
            return;
        }

        // Extract relative path from Supabase URL
        $prefix = rtrim($config['url'], '/') . '/storage/v1/object/public/' . $config['bucket'] . '/';
        if (Str::startsWith($path, $prefix)) {
            $relativePath = str_replace($prefix, '', $path);
            $url = rtrim($config['url'], '/') . '/storage/v1/object/' . $config['bucket'] . '/' . $relativePath;

            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $config['key'],
                    'apikey' => $config['key'],
                ])->delete($url);

                if (!$response->successful()) {
                    Log::error('Supabase delete failed: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Supabase delete exception: ' . $e->getMessage());
            }
        }
    }
}
