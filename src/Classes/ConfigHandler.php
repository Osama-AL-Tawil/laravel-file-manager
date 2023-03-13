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

    public static function getStorageUrl():string{
        if (config()->has('filesystems.disks.files')){
            return 'files/';
        }else{
            return 'storage';
        }
    }

}
