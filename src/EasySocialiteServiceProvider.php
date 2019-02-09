<?php

namespace Devmi\EasySocialite;

use Illuminate\Support\ServiceProvider;
use Devmi\EasySocialite\Http\Middlewares\AbortIfNotActivated;

class EasySocialiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/easysocialite.php' => config_path('easysocialite.php'),
        ], 'easysocialite');

        $this->registerMiddleware(
            AbortIfNotActivated::class
        );

    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/easysocialite.php', 'easysocialite'
        );
    }

    protected function registerMiddleware($middleware)
    {
        app('router')->aliasMiddleware('easysocialite.activated', $middleware);
    }
}
