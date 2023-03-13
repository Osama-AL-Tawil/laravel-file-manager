<?php

use Illuminate\Support\Facades\Route;

Route::get('/files/{path}',function ($path){

    return \OST\LaravelFileManager\FileManager::getFileByPath($path);
});
