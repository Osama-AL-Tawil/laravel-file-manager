<?php

namespace OST\LaravelFileManager\Classes;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class FileFunctions
{


    /**
     * Get file from storage by normal url
     * @throws FileNotFoundException
     */
    public static function getFileByUrl(string $url): StreamedResponse
    {
        $disk =  config('laravel_file_manager.disk');
        $file_path = self::getPathFromUrl($url)[0];
        return self::getFileByPath($file_path);
    }



    /**
     * Get file from storage by normal path
     * @throws FileNotFoundException
     */
    public static function getFileByPath(string $file_path): StreamedResponse
    {
        $disk =  config('laravel_file_manager.disk');
        $is_encrypted =  config('laravel_file_manager.encrypted_url');

        $file_path= $is_encrypted?Crypt::decryptString($file_path):$file_path;

        if (Storage::disk($disk)->exists($file_path)) {
            return Storage::disk($disk)->response($file_path);
        }

        throw  new FileNotFoundException('File Not Found');

    }




    /**
     * This function generate url by encrypt file path and concatenate base url with path
     * @param array|string|null $path
     * @param bool $with_type
     * @return array|null
     */
    public static function getUrl(array|string|null $path, bool $with_type = false): null|array
    {
        $paths = [];
        $is_encrypted =  config('laravel_file_manager.encrypted_url');

        if ($path) {
            if (!is_array($path)) {
                $path = [$path];
            }
            foreach ($path as $value) {
                $type = substr(strchr($value, '.'), 1); //get file type
                $file_path = $is_encrypted?Crypt::encryptString($value):$value;
                if ($with_type) {
                    $paths[] = ['url' => config('laravel_file_manager.url') . $file_path, 'type' => $type ];
                } else {
                    $paths[] = config('laravel_file_manager.url') . $file_path;
                }
            }
            return $paths;
        }

        return null;
    }


    /**
     * Get File path from url
     * @param string|array $url
     * @return array
     */
    protected static function getPathFromUrl(string|array $url): array
    {
        $is_encrypted = config('laravel_file_manager.encrypted_url');
        $prefix = config('laravel_file_manager.prefix');
        if (!is_array($url)) {
            $url = [$url];
        }
        $paths = [];
        foreach ($url as $value) {
            $path = substr($value, strrpos($value, $prefix )+strlen($prefix));
            if ($is_encrypted) {
                $paths[] = Crypt::decryptString($path);
            } else {
                $paths[] = $path;
            }
        }
        return $paths;

    }

    protected static function decryptPath(string|array $path):array{
        $paths = [];
        if (!is_array($path)){
           $path = [$path];
       }
       foreach ($path as $value){
           $paths[] = Crypt::decryptString($value);
       }
       return $paths;
    }


    /**
     * Get file Temp URL
     *
     * @param $path
     * @param int $time
     * @return string
     */
    public static function getTemporaryUrl($path, int $time = 5): string
    {
        $disk =  config('laravel_file_manager.disk');
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($time));
    }


    /**
     * Download selected file
     *
     * @param $path
     * @return StreamedResponse
     */
    public static function download($path):StreamedResponse
    {
        $disk = config('laravel_file_manager.disk');
        // if file name not in ASCII format
        if (!preg_match('/^[\x20-\x7e]*$/', basename($path))) {
            $filename = Str::ascii(basename($path));
        } else {
            $filename = basename($path);
        }

        return Storage::disk($disk)->download($path, $filename);
    }


    /**
     * Stream file - for audio and video
     *
     * @param $disk
     * @param $path
     *
     * @return StreamedResponse
     */
    public static function streamFile($path): StreamedResponse
    {
        $disk =  config('laravel_file_manager.disk');

        // if file name not in ASCII format
        if (!preg_match('/^[\x20-\x7e]*$/', basename($path))) {
            $filename = Str::ascii(basename($path));
        } else {
            $filename = basename($path);
        }

        return Storage::disk($disk)
            ->response($path, $filename, ['Accept-Ranges' => 'bytes']);
    }



}
