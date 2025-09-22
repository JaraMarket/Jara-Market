<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

function upload_image(string $folder, $image_file, ?string $old_file = null): ?string
{
    if (!$image_file) {
        return null;
    }

    $filename = time() . '_' . preg_replace('/\s+/', '_', $image_file->getClientOriginalName());

    // Determine environment
    $disk = app()->environment('production') ? 's3' : 'public';

    // Delete old file if exists
    if ($old_file && Storage::disk($disk)->exists($old_file)) {
        Storage::disk($disk)->delete($old_file);
    }

     // Unique filename
    $filename = time() . '_' . preg_replace('/\s+/', '_', $image_file->getClientOriginalName());

    $path = $image_file->storeAs($folder, $filename, $disk);

    return $path;
}

function delete_image($path)
{
    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
        return true;
    }
    return false;
}
if (!function_exists('get_media_url')) {
    /**
     * Resolve media URL with environment-aware fallback (S3 → Local → Null).
     *
     * @param string|null $path
     * @return string|null
     */
    function get_media_url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $disk = app()->environment('production') ? 's3' : 'public';

        return Storage::disk($disk)->exists($path)
            ? Storage::disk($disk)->url($path)
            : null;
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