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
            return config('filesystems.disks.files.url');
        }else{
            return config('filesystems.disks.public.url');
        }
    }

}
