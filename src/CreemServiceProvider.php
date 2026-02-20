<?php

namespace Creem\Laravel;

use Creem\Laravel\Commands\SyncProductsCommand;
use Creem\Laravel\Commands\WebhookSecretCommand;
use Creem\Laravel\Http\Middleware\VerifyCreemWebhook;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CreemServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/creem.php', 'creem');

        $this->app->singleton(CreemClient::class, function ($app) {
            $config = $app['config']['creem'];

            return new CreemClient(
                apiKey: $config['api_key'],
                baseUrl: $config['api_url'] ?? '',
            );
        });

        $this->app->singleton(Creem::class, function ($app) {
            return new Creem($app->make(CreemClient::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/creem.php' => config_path('creem.php'),
        ], 'creem-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'creem-migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                WebhookSecretCommand::class,
                SyncProductsCommand::class,
            ]);
        }

        $this->registerRoutes();
        $this->registerMiddleware();
    }

    /**
     * Register the webhook route.
     *
     * Uses the 'api' middleware group to avoid CSRF verification,
     * and adds rate limiting to prevent abuse.
     */
    protected function registerRoutes(): void
    {
        Route::middleware('api')
            ->post(
                config('creem.webhook_path', 'creem/webhook'),
                [Http\Controllers\WebhookController::class, 'handle']
            )
            ->middleware([VerifyCreemWebhook::class, 'throttle:120,1'])
            ->name('creem.webhook');
    }

    /**
     * Register the webhook verification middleware alias.
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        $router->aliasMiddleware('creem.webhook', VerifyCreemWebhook::class);
    }
}
