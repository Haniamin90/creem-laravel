<?php

namespace Creem\Laravel\Tests\Unit;

use Creem\Laravel\CreemClient;
use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Exceptions\CreemAuthenticationException;
use Creem\Laravel\Exceptions\CreemRateLimitException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class CreemClientTest extends TestCase
{
    protected function createClientWithMock(array $responses): CreemClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $httpClient = new Client(['handler' => $handlerStack]);

        $client = new CreemClient('creem_test_fake_key');
        $client->setHttpClient($httpClient);

        return $client;
    }

    public function test_resolves_sandbox_url_for_test_keys(): void
    {
        $client = new CreemClient('creem_test_abc123');
        $this->assertTrue($client->isSandbox());
        $this->assertEquals('https://test-api.creem.io', $client->getBaseUrl());
    }

    public function test_resolves_production_url_for_live_keys(): void
    {
        $client = new CreemClient('creem_live_abc123');
        $this->assertFalse($client->isSandbox());
        $this->assertEquals('https://api.creem.io', $client->getBaseUrl());
    }

    public function test_allows_custom_base_url(): void
    {
        $client = new CreemClient('creem_test_abc123', 'https://custom-api.example.com');
        $this->assertEquals('https://custom-api.example.com', $client->getBaseUrl());
    }

    public function test_get_request_returns_decoded_json(): void
    {
        $client = $this->createClientWithMock([
            new Response(200, [], json_encode(['id' => 'prod_123', 'name' => 'Test'])),
        ]);

        $result = $client->get('v1/products', ['id' => 'prod_123']);

        $this->assertEquals('prod_123', $result['id']);
        $this->assertEquals('Test', $result['name']);
    }

    public function test_post_request_returns_decoded_json(): void
    {
        $client = $this->createClientWithMock([
            new Response(200, [], json_encode(['checkout_url' => 'https://checkout.creem.io/123'])),
        ]);

        $result = $client->post('v1/checkouts', ['product_id' => 'prod_123']);

        $this->assertEquals('https://checkout.creem.io/123', $result['checkout_url']);
    }

    public function test_delete_request_returns_decoded_json(): void
    {
        $client = $this->createClientWithMock([
            new Response(200, [], json_encode(['deleted' => true])),
        ]);

        $result = $client->delete('v1/discounts/disc_123/delete');

        $this->assertTrue($result['deleted']);
    }

    public function test_empty_response_returns_empty_array(): void
    {
        $client = $this->createClientWithMock([
            new Response(204, [], ''),
        ]);

        $result = $client->get('v1/test');

        $this->assertEquals([], $result);
    }

    public function test_throws_authentication_exception_on_403(): void
    {
        $client = $this->createClientWithMock([
            new Response(403, [], json_encode([
                'error' => 'Forbidden',
                'message' => 'Invalid API key',
                'trace_id' => 'trace_abc123',
            ])),
        ]);

        $this->expectException(CreemAuthenticationException::class);
        $this->expectExceptionMessage('Invalid API key');

        $client->get('v1/products');
    }

    public function test_throws_rate_limit_exception_on_429(): void
    {
        $client = $this->createClientWithMock([
            new Response(429, [], json_encode([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded',
            ])),
        ]);

        $this->expectException(CreemRateLimitException::class);

        $client->get('v1/products');
    }

    public function test_throws_api_exception_on_400(): void
    {
        $client = $this->createClientWithMock([
            new Response(400, [], json_encode([
                'error' => 'Bad Request',
                'message' => ['name is required', 'price is required'],
                'trace_id' => 'trace_xyz',
            ])),
        ]);

        try {
            $client->post('v1/products', []);
            $this->fail('Expected CreemApiException');
        } catch (CreemApiException $e) {
            $this->assertStringContainsString('name is required', $e->getMessage());
            $this->assertStringContainsString('price is required', $e->getMessage());
            $this->assertEquals('trace_xyz', $e->getTraceId());
            $this->assertEquals(400, $e->getCode());
        }
    }

    public function test_throws_api_exception_on_404(): void
    {
        $client = $this->createClientWithMock([
            new Response(404, [], json_encode([
                'error' => 'Not Found',
                'message' => 'Resource not found',
            ])),
        ]);

        $this->expectException(CreemApiException::class);
        $this->expectExceptionCode(404);

        $client->get('v1/products', ['id' => 'nonexistent']);
    }

    public function test_authentication_exception_includes_trace_id(): void
    {
        $client = $this->createClientWithMock([
            new Response(403, [], json_encode([
                'error' => 'Forbidden',
                'message' => 'Invalid API key',
                'trace_id' => 'trace_auth_123',
            ])),
        ]);

        try {
            $client->get('v1/products');
            $this->fail('Expected CreemAuthenticationException');
        } catch (CreemAuthenticationException $e) {
            $this->assertEquals('trace_auth_123', $e->getTraceId());
        }
    }

    public function test_throws_authentication_exception_on_401(): void
    {
        $client = $this->createClientWithMock([
            new Response(401, [], json_encode([
                'error' => 'Unauthorized',
                'message' => 'Missing API key',
            ])),
        ]);

        $this->expectException(CreemAuthenticationException::class);
        $this->expectExceptionMessage('Missing API key');
        $this->expectExceptionCode(401);

        $client->get('v1/products');
    }

    public function test_rate_limit_exception_includes_retry_after(): void
    {
        $client = $this->createClientWithMock([
            new Response(429, ['Retry-After' => '30'], json_encode([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded',
            ])),
        ]);

        try {
            $client->get('v1/products');
            $this->fail('Expected CreemRateLimitException');
        } catch (CreemRateLimitException $e) {
            $this->assertEquals(30, $e->getRetryAfter());
            $this->assertEquals(429, $e->getCode());
        }
    }

    public function test_rate_limit_exception_without_retry_after_header(): void
    {
        $client = $this->createClientWithMock([
            new Response(429, [], json_encode([
                'error' => 'Too Many Requests',
                'message' => 'Rate limit exceeded',
            ])),
        ]);

        try {
            $client->get('v1/products');
            $this->fail('Expected CreemRateLimitException');
        } catch (CreemRateLimitException $e) {
            $this->assertNull($e->getRetryAfter());
        }
    }
}
