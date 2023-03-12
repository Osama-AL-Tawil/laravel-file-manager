<?php

namespace OST\LaravelFileManager;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use OST\LaravelFileManager\Classes\FileManager;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    public function boot(){


        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

//        if ($this->app->runningInConsole()) {
//            // Export the migration
//            if (! class_exists('OST\LaravelFileManager\database\migrations\CreateFilesTable')) {
//                $this->publishes([
//                    __DIR__ . '/../database/migrations/create_files_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_posts_table.php'),
//                    // you can add any number of migrations here
//                ], 'migrations');
//            }
//        }
    }

    public function register()
    {
        $this->app->bind('file_manager_facade',function ($app){
            return new FileManager();
        });

        $this->app->bind('calculator',function ($app){
            return new Calculator();
        });
//        $this->app['file_manager_facade'] = $this->app->
//        $this->app->bind('file_manager_facade',FileManager::class);
//
//        $this->app->bind('calculator',Calculator::class);
//        $loader = AliasLoader::getInstance();
//        $loader->alias('FileManager',FileManager::class);
//        $loader->alias('Calculator',Calculator::class);

    }

}
