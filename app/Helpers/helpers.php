<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

function upload_image(string $folder, $image_file, ?string $old_file = null): ?string
{
    if (!$image_file) {
        return null;
    }

    // Use environment-based disk (s3 in production, public otherwise)
    $disk = app()->environment('production') ? 's3' : 'public';

    // Build safe unique filename
    $filename = time() . '_' . preg_replace('/\s+/', '_', $image_file->getClientOriginalName());

    try {
        // Delete old file if exists
        if ($old_file && Storage::disk($disk)->exists($old_file)) {
            Storage::disk($disk)->delete($old_file);
        }

        // Upload to the correct disk
        $path = $image_file->storeAs($folder, $filename, $disk);
        
        return $path; // e.g., logo/169598xxxx_logo.png

    } catch (\Exception $e) {
        // If something goes wrong (S3 misconfig, permissions, etc.), log it
        \Log::error('Image upload failed', [
            'disk' => $disk,
            'file' => $filename,
            'error' => $e->getMessage(),
        ]);

        return null;
    }
}


function delete_image($path)
{
    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
        return true;
    }
    return false;
}
function get_media_url(?string $path): ?string
{
    if (!$path) {
        return null;
    }

    // Detect environment
    $disk = app()->environment('production') ? 's3' : 'public';

    try {
        // Check if file exists on the disk (return null if missing)
        if (!Storage::disk($disk)->exists($path)) {
            return null;
        }

        // If found, get the URL
        $url = Storage::disk($disk)->url($path);

        // Extra handling for cPanel/public storage issues
        if ($disk === 'public') {
            // Rewrite if Laravel generated a "storage/app/public" path
            $url = str_replace('/storage/app/public', '/storage', $url);

            // If symlink is broken/missing, fallback to manual asset() build
            if (!file_exists(public_path('storage'))) {
                $url = asset('storage/app/public/' . ltrim($path, '/'));
            }
        }

        return $url;

    } catch (\Exception $e) {
        // Catch S3 misconfiguration or Flysystem errors → return null
        return null;
    }
}

if (!function_exists('company')) {
    function company(?string $key = null, $default = null)
    {
        static $settings;

        if (!$settings) {
            $settings = DB::table('settings')
                ->whereIn('key', [
                    'site_name',
                    'company_logo',
                    'favicon_logo',
                    'site_description',
                    'contact_email',
                    'contact_phone',
                    'address',
                ])
                ->pluck('value', 'key')
                ->toArray();
        }

        if ($key) {
            return $settings[$key] ?? $default;
        }

        return $settings;
    }
}