<?php

namespace OST\LaravelFileManager\Exceptions;

use InvalidArgumentException;

class DiskNotFoundException extends InvalidArgumentException
{

    public static function create($disk)
    {
        return new static('The ['.$disk.'] disk not found,The disk must be in (filesystem)',404);
    }
}
