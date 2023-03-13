<?php

namespace OST\LaravelFileManager\Classes;

class ConfigHandler
{

    public static function getDisk():string{
        if (config()->has('filesystems.disks.files')){
            return 'files';
        }else{
          return 'public';
        }
    }

    public static function getUrl():string{
        if (config()->has('filesystems.disks.files')){
            if (config()->has('filesystems.disks.files.url')){
                return config('filesystems.disks.public.url').'/';
            }
            return asset('/files/');
        }else{
            if (config()->has('filesystems.disks.public.url')){
                return config('filesystems.disks.public.url').'/';
            }
            return asset('/storage/');
        }
    }

}
