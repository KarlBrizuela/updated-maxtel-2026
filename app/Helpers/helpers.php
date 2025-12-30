<?php


use Illuminate\Support\Facades\Log;

if (!function_exists('asset_with_env')) {
    function asset_with_env($path)
    {
        $environment = env('APP_ENV');
        Log::info('Current APP_ENV: ' . $environment);
        
        // Remove any leading slashes from the path
        $path = ltrim($path, '/');
        
        // In production, assets should be in the public directory directly
        // We don't need to add 'public/' prefix as it's already in the URL structure
        return asset($path);
    }
}
