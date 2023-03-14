<?php

namespace OST\LaravelFileManager\Exceptions;

use InvalidArgumentException;

class DiskUrlNotFoundException extends InvalidArgumentException
{

    public static function create($disk = null)
    {
        $disk = $disk?:config('laravel_file_manager.disk');
        return new static('The url for ['.$disk.'] disk not found',404);
    }
}
