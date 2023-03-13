<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

if (config('laravel_file_manager.disk') == 'files') {
    Route::get('/files/{path}', function ($path) {

        try {
            $disk = config('laravel_file_manager.disk');
            $is_encrypted = config('laravel_file_manager.encrypted_url');

            $file_path = $is_encrypted ? Crypt::decryptString($path) : $path;

            if (Storage::disk($disk)->exists($file_path)) {
                return Storage::disk($disk)->response($file_path);
            } else {
                abort(404, 'File Not Found');
            }
        }catch (Exception $exception){
            abort(404, $exception->getMessage());
        }
    })->where('path', '.*');

}

