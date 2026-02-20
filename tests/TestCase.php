<?php

namespace Creem\Laravel\Tests;

use Creem\Laravel\CreemServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CreemServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Creem' => \Creem\Laravel\Facades\Creem::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('creem.api_key', 'creem_test_fake_key_for_testing');
        $app['config']->set('creem.webhook_secret', 'test_webhook_secret_123');
        $app['config']->set('creem.api_url', 'https://test-api.creem.io');
    }
}
