<?php

namespace OST\LaravelFileManager;

use Illuminate\Support\ServiceProvider;

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
        $this->app->bind('file_manager_facade', function() {
            return new \OST\LaravelFileManager\Classes\FileManager();
        });

        $this->app->bind('calculator', function($app) {
            return new Calculator();
        });
    }

}
