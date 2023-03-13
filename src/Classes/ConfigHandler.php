<?php

namespace OST\LaravelFileManager\Classes;

class ConfigHandler
{

    public static function getDisk():string{
        if (config('filesystems.default') == 'local'){
            return 'public';
        }else{
            return config('filesystems.default');
        }
    }

    public static function getUrl():string{
        $key = 'filesystems.disks.'.self::getDisk().'.url';
        return config($key).'/';
    }

    public static function getPrefix():string{
        return substr(parse_url(self::getUrl())['path'],1);
    }

}
