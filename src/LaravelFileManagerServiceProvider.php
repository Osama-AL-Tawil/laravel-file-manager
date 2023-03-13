<?php

namespace OST\LaravelFileManager;

use Illuminate\Support\ServiceProvider;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    public function boot(){
        $this->publishMigrations();
//        if ($this->app->runningInConsole()) {
//            // Export the migration
//            if (! class_exists('CreatePostsTable')) {
//                $this->publishes([
//                    __DIR__ . '/../database/migrations/create_posts_table.php.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_posts_table.php'),
//                    // you can add any number of migrations here
//                ], 'migrations');
//            }
//        }
//        $this->publishes([
//            __DIR__.'/../src/database/migrations/create_files_table.php.stub'=>
//            $this->app->databasePath('migrations/'.date('Y_m_d_His', time()).'_create_files_table.php'),
//        ],'migrations');

        //$this->loadMigrationsFrom(__DIR__ . '/database/migrations');

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

    }



    private function mergeConfig()
    {
        $path = $this->getConfigPath();
        $this->mergeConfigFrom($path, 'bar');
    }

    private function publishConfig()
    {
        $path = $this->getConfigPath();
        $this->publishes([$path => config_path('bar.php')], 'config');
    }

    private function publishMigrations()
    {
        $path = $this->getMigrationsPath();
        $this->publishes([$path => database_path('migrations')], 'migrations');
    }

    private function getConfigPath()
    {
        return __DIR__ . '/../config/bar.php';
    }

    private function getMigrationsPath()
    {
        return __DIR__ . '/../database/migrations/';
    }
}
