<?php

namespace OST\LaravelFileManager\Exceptions;



class FileNotFoundException  extends \Nette\FileNotFoundException
{

    public static function create()
    {
        return new static('The file you are trying to access through this link is not available ',404);
    }
}
