<?php

namespace Creem\Laravel\Tests\Feature;

use Creem\Laravel\Tests\TestCase;

class CommandTest extends TestCase
{
    public function test_webhook_secret_show_with_configured_secret(): void
    {
        $this->artisan('creem:webhook-secret', ['--show' => true])
            ->expectsOutput('Current CREEM webhook secret:')
            ->expectsOutput('test_webhook_secret_123')
            ->assertExitCode(0);
    }

    public function test_webhook_secret_show_without_configured_secret(): void
    {
        config(['creem.webhook_secret' => '']);

        $this->artisan('creem:webhook-secret', ['--show' => true])
            ->expectsOutput('No CREEM webhook secret is configured.')
            ->assertExitCode(1);
    }

    public function test_webhook_secret_generate_fails_when_existing(): void
    {
        $this->artisan('creem:webhook-secret')
            ->expectsOutput('A CREEM webhook secret already exists.')
            ->assertExitCode(1);
    }

    public function test_sync_products_command_is_registered(): void
    {
        $this->assertTrue(
            $this->app['Illuminate\Contracts\Console\Kernel']
                ->all()['creem:sync-products'] !== null
        );
    }

    public function test_webhook_secret_command_is_registered(): void
    {
        $this->assertTrue(
            $this->app['Illuminate\Contracts\Console\Kernel']
                ->all()['creem:webhook-secret'] !== null
        );
    }
}
