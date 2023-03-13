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
    public static function getFileByUrl(string $url, $disk = null): StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');
        $file_path = self::getPathFromUrl($url)[0];
        return self::getFileByPath($file_path, $disk);
    }


    /**
     * Get file from storage by encrypted url
     * @throws FileNotFoundException
     */
    public static function getFileByEncryptedUrl($encrypted_url, $disk = null): StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');
        $file_path = self::getPathFromEncryptedUrl($encrypted_url)[0];
        return self::getFileByPath($file_path, $disk);
    }

    /**
     * Get file from storage by encrypted file  path
     * @throws FileNotFoundException
     */
    public static function getFileByEncryptedPath(string $encrypted_file_path, $disk = null): StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');
        $file_path = Crypt::decryptString($encrypted_file_path);
        return self::getFileByPath($file_path, $disk);
    }



    /**
     * Get file from storage by normal path
     * @throws FileNotFoundException
     */
    public static function getFileByPath(string $file_path, $disk = null): StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');

        if (Storage::disk($disk)->exists($file_path)) {
            return Storage::disk($disk)->response($file_path);
        }

        $message = '[' . trans('app.app_name') . '|Storage] File Not Found';

        throw  new FileNotFoundException($message);

    }


    /**
     * Get File url
     * @param array|string|null $path
     * @param bool $with_type
     * @return array|string|null
     */
    public static function getUrl(array|string|null $path, bool $with_type = false):array|string|null{
        $paths = [];
        if ($path) {
            if (!is_array($path)) {
                $path = [$path];
            }
            foreach ($path as $value) {
                $type = substr(strchr($value, '.'), 1);
                if ($with_type) {
                    $paths[] = [
                        'url' => asset(config('laravel_file_manager.storage_url') . $value),
                        'type' => $type
                    ];
                } else {
                    $paths[] = asset(config('laravel_file_manager.storage_url') . $value);
                }
            }
            return $paths;
        }
        return null;
    }


    /**
     * This function generate url by encrypt file path and concatenate base url with path
     * @param array|string|null $path
     * @param bool $with_type
     * @param $default_url
     * @return string|array|null
     */
    public static function getEncryptedUrl(array|string|null $path, bool $with_type = false, $default_url = null): null|string|array
    {
        $paths = [];

        if ($path) {
            if (!is_array($path)) {
                $path = [$path];
            }
            foreach ($path as $value) {
                $type = substr(strchr($value, '.'), 1);
                $file_path = Crypt::encryptString($value);
                if ($with_type) {
                    $paths[] = [
                        'url' => asset(config('laravel_file_manager.storage_url') . $file_path),
                        'type' => $type
                    ];
                } else {
                    $paths[] = asset(config('laravel_file_manager.storage_url') . $file_path);
                }
            }
            return $paths;
        } else {
            if ($default_url) {
                return $default_url;
            }
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
        $paths = [];
        if (!is_array($url)) {
            $url = [$url];
        }
        foreach ($url as $value) {
            $encrypted_path = strchr($value, config('laravel_file_manager.storage_url')); //get encrypted path from url
            $paths[] = substr($encrypted_path, 6); //cut 'files/' length 6
        }
        return $paths;
    }


    /**
     * Get file path from encrypted url
     * @param string|array $url
     * @return array
     */
    protected static function getPathFromEncryptedUrl(string|array $url): array
    {
        $paths = [];
        if (!is_array($url)) {
            $url = [$url];
        }
        foreach ($url as $value) {
            $encrypted_path = strchr($value, config('laravel_file_manager.storage_url')); //get encrypted path from url
            $encrypted_path = substr($encrypted_path, 6); //cut 'files/' length 6
            $paths[] = Crypt::decryptString($encrypted_path);
        }
        return $paths;
    }



    /**
     * Get file Temp URL
     *
     * @param $path
     * @param null $disk
     * @param int $time
     * @return string
     */
    public static function temporaryUrl($path, $disk = null, int $time = 5): string
    {
        $disk = $disk ?: config('laravel_file_manager.disk');
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($time));
    }


    /**
     * Download selected file
     *
     * @param $path
     * @param null $disk
     * @return StreamedResponse
     */
    public static function download($path, $disk = null):StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');
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
    public static function streamFile($path, $disk = null): StreamedResponse
    {
        $disk = $disk ?: config('laravel_file_manager.disk');

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
