<?php

namespace OST\LaravelFileManager\Helpers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OST\LaravelFileManager\Exceptions\DiskNotFoundException;
use OST\LaravelFileManager\Exceptions\DiskUrlNotFoundException;
use OST\LaravelFileManager\Exceptions\FileNotFoundException;
use Symfony\Component\HttpFoundation\StreamedResponse;

abstract class FileFunctions
{

    /**
     * This function generate url by encrypt file path and concatenate base url with path
     * @param array|string|null $path
     * @param bool $with_type
     * @param null $disk
     * @return array|null
     */
    public static function getUrl(array|string|null $path, $disk = null, bool $with_type = false): null|array
    {
        $paths = [];
        $is_encrypted = config('laravel_file_manager.encrypted_url');
        $disk = $disk ?: config('laravel_file_manager.disk');

        if ($path) {
            if (!is_array($path)) {
                $path = [$path];
            }
            foreach ($path as $value) {
                $type = substr(strchr($value, '.'), 1); //get file type
                $file_path = $is_encrypted ? Crypt::encryptString($value) : $value;

                if ($disk) {
                    //for custom disk
                    $url = self::getUrlFromCustomDisk($disk) . '/';
                } else {
                    //for default disk
                    $url = config('laravel_file_manager.url');
                }
                if ($with_type) {
                    $paths[] = ['url' => $url . $file_path, 'type' => $type];
                } else {
                    $paths[] = $url . $file_path;
                }
            }


            return $paths;
        }

        return null;
    }



    /**
     * Get disk url
     * @param $disk
     * @return string
     */
    private function getUrlFromCustomDisk($disk):string{
        if (config('filesystems.disks.'.$disk)){
            if (config('filesystems.disks.'.$disk.'.url')){
                return config('filesystems.disks.'.$disk.'.url');
            }else{
                throw DiskUrlNotFoundException::create($disk);
            }
        }else{
            throw DiskNotFoundException::create($disk);
        }
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

    /**
     * Decrypt single or multiple string path
     * @param string|array $path
     * @return array
     */
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
     * @param string $path
     * @param int $time
     * @param string|null $disk
     * @return string
     */
    public static function getTemporaryUrl(string $path, int $time = 5,string $disk = null): string
    {
        $disk =  $disk?:config('laravel_file_manager.disk');
        return Storage::disk($disk)->temporaryUrl($path, now()->addMinutes($time));
    }


    /**
     * Download selected file
     *
     * @param string $path
     * @param string|null $disk
     * @return StreamedResponse
     */
    public static function download(string $path,string $disk=null):StreamedResponse
    {
        $disk =  $disk?:config('laravel_file_manager.disk');
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
     * @param string $path
     * @param string|null $disk
     * @return StreamedResponse
     */
    public static function streamFile(string $path,string $disk = null): StreamedResponse
    {
        $disk =  $disk?:config('laravel_file_manager.disk');

        // if file name not in ASCII format
        if (!preg_match('/^[\x20-\x7e]*$/', basename($path))) {
            $filename = Str::ascii(basename($path));
        } else {
            $filename = basename($path);
        }

        return Storage::disk($disk)
            ->response($path, $filename, ['Accept-Ranges' => 'bytes']);
    }


    /**
     * Get file from storage by  url
     * This function using in route to stream file
     * @param string $url
     * @param string $disk
     * @param bool $is_encrypted
     * @return StreamedResponse
     */
    public function getFileByUrlCustom(string $url , string $disk , bool $is_encrypted): StreamedResponse
    {
        $disk_url =  $this->getUrlFromCustomDisk($disk);
        $prefix=substr(parse_url($disk_url)['path'],1);
        $path = substr($url, strrpos($url, $prefix )+strlen($prefix));


        $file_path= $is_encrypted?Crypt::decryptString($path):$path;

        if (Storage::disk($disk)->exists($file_path)) {
            return Storage::disk($disk)->response($file_path);
        }

        throw  FileNotFoundException::create();

    }



}
