<?php

namespace OST\LaravelFileManager\Classes;

use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class FileFunctions
{


    /**
     * Get file from storage
     * @throws Exception
     */
    public static function getFileByPath(string $file_path,string $disk=null): StreamedResponse
    {
        $disk = $disk?:config('filesystems.default');

        $file_path = Crypt::decryptString($file_path);

        if (Storage::disk($disk)->exists($file_path)) {
            return Storage::disk($disk)->response($file_path);
        }

        $message = '[' . trans('app.app_name') . '|Storage] File Not Found';
        throw new Exception($message);

    }



    public static function generateEncryptedUrl(array|string|null $path, bool $with_type=false, $default_url = null): null|string|array
    {
        $paths = [];

        if ($path) {
            if (!is_array($path)){
                $path = [$path];
            }
            foreach ($path as $value){
                $type = substr(strchr($value, '.'), 1);
                $file_path = Crypt::encryptString($value);
                if ($with_type) {
                    $paths[] =  [
                        'url' => asset(config('filesystems.storage_url') . $file_path),
                        'type' => $type
                    ];
                } else {
                    $paths[] =  asset(config('filesystems.storage_url') . $file_path);
                }
            }
            return $paths;
        }else{
            if ($default_url) {
                return $default_url;
            }
        }

        return null;
    }


    protected static function getPathFromEncryptedUrl(string|array $url):array{
        $paths = [];
        if (!is_array($url)){
            $url = [$url];
        }
        foreach ($url as $value){
            $encrypted_path = strchr($value,config('filesystems.storage_url')); //get encrypted path from url
            $encrypted_path = substr($encrypted_path,6); //cut 'files/' length 6
            $paths[] = Crypt::decryptString($encrypted_path);
        }
        return $paths;
    }


    protected static function getPathFromUrl(string|array $url):array{
        $paths = [];
        if (!is_array($url)){
            $url = [$url];
        }
        foreach ($url as $value){
            $encrypted_path = strchr($value,config('filesystems.storage_url')); //get encrypted path from url
            $paths[] = substr($encrypted_path,6); //cut 'files/' length 6
        }
        return $paths;
    }

    /**
     * Get file URL
     *
     * @param $path
     * @param null $disk
     * @return string
     */
    public static function url($path, $disk = null): string
    {
        $disk = $disk?:config('filesystems.default');
        return Storage::disk($disk)->url($path);
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
        $disk = $disk?:config('filesystems.default');
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($time));
    }




    /**
     * Download selected file
     *
     * @param $path
     * @param $disk
     *
     * @return StreamedResponse
     */
    public static function download($path, $disk = null): StreamedResponse
    {
        $disk = $disk?:config('filesystems.default');
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
        $disk = $disk?:config('filesystems.default');

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
