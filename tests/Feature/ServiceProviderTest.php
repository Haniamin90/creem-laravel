<?php

namespace Creem\Laravel\Tests\Feature;

use Creem\Laravel\Creem;
use Creem\Laravel\CreemClient;
use Creem\Laravel\Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_creem_client_is_bound_as_singleton(): void
    {
        $client1 = $this->app->make(CreemClient::class);
        $client2 = $this->app->make(CreemClient::class);

        $this->assertSame($client1, $client2);
    }

    public function test_creem_is_bound_as_singleton(): void
    {
        $creem1 = $this->app->make(Creem::class);
        $creem2 = $this->app->make(Creem::class);

        $this->assertSame($creem1, $creem2);
    }

    public function test_config_is_loaded(): void
    {
        $this->assertEquals('creem_test_fake_key_for_testing', config('creem.api_key'));
        $this->assertEquals('test_webhook_secret_123', config('creem.webhook_secret'));
    }

    public function test_default_config_values(): void
    {
        $this->assertEquals('creem/webhook', config('creem.webhook_path'));
        $this->assertEquals(300, config('creem.webhook_tolerance'));
        $this->assertEquals('USD', config('creem.currency'));
        $this->assertEquals('App\\Models\\User', config('creem.customer_model'));
    }

    public function test_webhook_route_is_registered(): void
    {
        $routes = $this->app['router']->getRoutes();
        $webhookRoute = $routes->getByName('creem.webhook');

        $this->assertNotNull($webhookRoute);
        $this->assertEquals('creem/webhook', $webhookRoute->uri());
        $this->assertContains('POST', $webhookRoute->methods());
    }

    public function test_facade_resolves_correctly(): void
    {
        $creem = \Creem\Laravel\Facades\Creem::getFacadeRoot();
        $this->assertInstanceOf(Creem::class, $creem);
    }

    public function test_creem_client_uses_sandbox_url(): void
    {
        $client = $this->app->make(CreemClient::class);
        $this->assertEquals('https://test-api.creem.io', $client->getBaseUrl());
    }
}
