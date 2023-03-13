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
        if (self::getDisk() =='files'){
            if (config()->has('filesystems.disks.files.url')){
                return config('filesystems.disks.files.url').'/';
            }
            return asset('/files/');
        }else {
            if (config()->has('filesystems.disks.public.url')){
                return config('filesystems.disks.public.url').'/';
            }
            return asset('/storage/');
        }
    }

    public static function getPrefix():string{
        if (self::getDisk() =='files'){
          return 'files/';
        }else {
           return 'storage/';
        }
    }

}
