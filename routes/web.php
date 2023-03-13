<?php

use Illuminate\Support\Facades\Route;

if (config('laravel_file_manager.disk') == 'files') {
    Route::get('/files/{path}', function ($path) {
        return \OST\LaravelFileManager\FileManager::getFileByPath($path);
    })->where('path', '.*');

}

