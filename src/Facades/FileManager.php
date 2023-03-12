<?php
namespace OST\LaravelFileManager\Facades;
use Illuminate\Support\Facades\Facade;

class FileManager extends Facade
{
     protected static function getFacadeAccessor()
     {
         return 'file_manager_facade';
     }
}
