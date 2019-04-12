<?php

namespace Lava\Filepond;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class FilepondServiceProvider extends ServiceProvider
{
    /**
     * Console commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Perform post-registration booting of services
     *
     * @return void
     */
    public function boot()
    {
    	$this->registerResources();

        if ($this->app->runningInConsole())
        {
            $this->registerPublishing();

            if ($commands = $this->commands) {
                $this->commands($commands);
            }
        }
    }

    /**
     * Register the package services and commands
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/filepond.php', 'filepond');

        $this->app->singleton('filepond', function () {
            return new Filepond;
        });
    }

    /**
     * Register the package resources such as routes, views...
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'filepond');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'filepond');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerRoutes();
    }

    /**
     * Register the package routes
     *
     * @return void
     */
    protected function registerRoutes()
    {
    	$config = $this->app['config']->get('filepond', []);

        $options = [
            'namespace'  => 'Lava\Filepond\Http\Controllers',
            'prefix'     => $config['prefix'] ?? null,
            'as'         => 'filepond',
         // 'middleware' => 'filepond',
        ];

        Route::group($options, function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the package's publishable resources
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/../config/filepond.php' => config_path('filepond.php'),
        ], 'filepond-config');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/filepond'),
        ], 'filepond-lang');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/filepond'),
        ], 'filepond-views');
    }

}
