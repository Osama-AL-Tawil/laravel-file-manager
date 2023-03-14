<?php

return[
    'disk' => \OST\LaravelFileManager\Helpers\ConfigHandler::getDisk(),
    'url'=> \OST\LaravelFileManager\Helpers\ConfigHandler::getUrl(),
    'prefix'=>\OST\LaravelFileManager\Helpers\ConfigHandler::getPrefix(),
    'encrypted_url'=>false
];
