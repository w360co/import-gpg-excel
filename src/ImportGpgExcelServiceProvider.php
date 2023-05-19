<?php

namespace W360\ImportGpgExcel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class ImportGpgExcelServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->registerResources();
        $this->registerPublishing();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Register evens
        $this->app->register(EventServiceProvider::class);


        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'gnupg');


        // Register the main class to use with the facade
        $this->app->singleton('ImportGPG', function () {
            return new ImportService;
        });

    }

    /**
     * register resources
     */
    private function registerResources()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/create_imports_table.php.stub' => $this->getMigrationFileName('create_imports_table.php'),
            __DIR__ . '/../database/migrations/create_failed_jobs_table.php.stub' => $this->getMigrationFileName('create_failed_jobs_table.php'),
            __DIR__ . '/../database/migrations/create_jobs_table.php.stub' => $this->getMigrationFileName('create_jobs_table.php'),

        ], 'migrations');
    }

    /**
     * register publishing
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('gnupg.php'),
            ], 'config');
        }
    }

    /**
     * Returns existing migration file if found, else uses the current timestamp.
     *
     * @param $migrationFileName
     * @return string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
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
