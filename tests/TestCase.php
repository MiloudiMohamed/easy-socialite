<?php

namespace Devmi\EasySocialite\Tests;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Devmi\EasySocialite\Http\Controllers\Auth\SocialLoginController;
use Devmi\EasySocialite\Http\Middlewares\AbortIfNotActivated;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->loadMigrationsFrom(realpath(__DIR__ . '/../database/migrations'));

        $this->setUpRoutes();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);

        $this->setCustomConfig($app);

        $kernel = $app->make(Kernel::class);
        $kernel->pushMiddleware(StartSession::class);
    }

    protected function setCustomConfig($app)
    {
        $app['config']->set('easysocialite.providers', [
            'github' => [
                'name' => 'GitHub',
            ],
            'twitter' => [
                'name' => 'Twitter',
            ]
        ]);

        $app['config']->set('easysocialite.model', [
            'path' => '\Devmi\EasySocialite\Tests\Models\User'
        ]);
    }

    protected function setUpRoutes()
    {
        Route::name('social.login')->get('/login/{provider}',
            SocialLoginController::class.'@redirectToProvider'
        )->middleware(AbortIfNotActivated::class);

        Route::name('social.login.callback')->get('/login/{provider}/callback',
            SocialLoginController::class.'@handleProviderCallback'
        )->middleware(AbortIfNotActivated::class);

    }
}
