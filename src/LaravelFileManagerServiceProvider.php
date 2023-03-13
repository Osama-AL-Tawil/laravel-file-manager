<?php

namespace OST\LaravelFileManager;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class LaravelFileManagerServiceProvider extends ServiceProvider
{
    public function boot(){

        $this->publishes([
            __DIR__.'/../database/migrations/create_files_table.php.stub' => $this->getMigrationFileName('create_files_table.php'),
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../config/laravel_file_manager.php' => config_path('laravel_file_manager.php'),
        ], 'config');

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel_file_manager.php', 'laravel_file_manager');

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


    protected function getMigrationFileName($migrationFileName): string
    {
        $timestamp = date('Y_m_d_His');

        $filesystem = $this->app->make(Filesystem::class);

        return Collection::make($this->app->databasePath().DIRECTORY_SEPARATOR.'migrations'.DIRECTORY_SEPARATOR)
            ->flatMap(function ($path) use ($filesystem, $migrationFileName) {
                return $filesystem->glob($path.'*_'.$migrationFileName);
            })
            ->push($this->app->databasePath()."/migrations/{$timestamp}_{$migrationFileName}")
            ->first();
    }


}
