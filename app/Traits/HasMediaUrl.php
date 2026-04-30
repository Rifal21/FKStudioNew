<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait HasMediaUrl
{
    public function getUrl($path)
    {
        \Illuminate\Support\Facades\Log::info("getUrl Entry", ['path' => $path]);
        if (!$path) return null;

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $publicUrl = config('services.nextcloud.public_url');

        if ($publicUrl && !Storage::disk('public')->exists($path)) {
            // Extract base domain: https://domain.com
            if (preg_match('~(https?://[^/]+)~', $publicUrl, $domainMatch)) {
                $baseDomain = rtrim($domainMatch[1], '/');
            } else {
                $baseDomain = rtrim($publicUrl, '/');
            }

            // Extract the share token: /s/TOKEN
            if (preg_match('~/s/([^/?#]+)~', $publicUrl, $tokenMatch)) {
                $token = $tokenMatch[1];
            } else {
                return $baseDomain . '/s/' . basename($publicUrl) . '/download?path=%2F&files=' . rawurlencode(basename($path));
            }

            // Remove NEXTCLOUD_ROOT from path for public access
            $root = config('services.nextcloud.root');
            $relativePath = $path;
            if ($root && str_starts_with($path, $root)) {
                $relativePath = ltrim(substr($path, strlen($root)), '/');
            }


            $pathSegments = explode('/', trim($relativePath, '/'));
            $encodedPath = implode('/', array_map('rawurlencode', $pathSegments));

            $finalUrl = $baseDomain . '/public.php/dav/files/' . $token . '/' . $encodedPath;
            
            \Illuminate\Support\Facades\Log::info("HasMediaUrl Debug", [
                'input_path' => $path,
                'relative_path' => $relativePath,
                'token' => $token,
                'final_url' => $finalUrl
            ]);

            return $finalUrl;


        }

        return Storage::disk('public')->url($path);
    }

    // Default media_url accessor for models with 'image' column
    public function getMediaUrlAttribute()
    {
        $column = property_exists($this, 'mediaColumn') ? $this->mediaColumn : 'image';
        return $this->getUrl($this->{$column});
    }
}
