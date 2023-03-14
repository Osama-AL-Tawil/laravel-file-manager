<?php

namespace OST\LaravelFileManager\Exceptions;

use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

class ParametersMissingException extends ValidationException
{

    public static function create( $validator)
    {
        return new static($validator);
    }
}
