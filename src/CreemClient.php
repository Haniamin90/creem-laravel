<?php

namespace Creem\Laravel;

use Creem\Laravel\Exceptions\CreemApiException;
use Creem\Laravel\Exceptions\CreemAuthenticationException;
use Creem\Laravel\Exceptions\CreemRateLimitException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;

class CreemClient
{
    protected Client $http;

    protected string $apiKey;

    protected string $baseUrl;

    protected const PRODUCTION_URL = 'https://api.creem.io';

    protected const SANDBOX_URL = 'https://test-api.creem.io';

    public function __construct(string $apiKey, string $baseUrl = '')
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl ?: $this->resolveBaseUrl($apiKey);

        $this->http = new Client([
            'base_uri' => rtrim($this->baseUrl, '/').'/',
            'headers' => [
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 30,
        ]);
    }

    /**
     * Determine the base URL based on the API key prefix.
     */
    protected function resolveBaseUrl(string $apiKey): string
    {
        if (str_starts_with($apiKey, 'creem_test_')) {
            return self::SANDBOX_URL;
        }

        return self::PRODUCTION_URL;
    }

    /**
     * Check if the client is using sandbox/test mode.
     */
    public function isSandbox(): bool
    {
        return $this->baseUrl === self::SANDBOX_URL;
    }

    /**
     * Get the configured base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Send a GET request to the CREEM API.
     *
     * @param  string  $endpoint  The API endpoint (e.g., 'v1/products').
     * @param  array<string, mixed>  $query  Query parameters.
     * @return array<string, mixed>
     *
     * @throws CreemApiException
     */
    public function get(string $endpoint, array $query = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $query]);
    }

    /**
     * Send a POST request to the CREEM API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $data  Request body data.
     * @return array<string, mixed>
     *
     * @throws CreemApiException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Send a DELETE request to the CREEM API.
     *
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $data  Request body data.
     * @return array<string, mixed>
     *
     * @throws CreemApiException
     */
    public function delete(string $endpoint, array $data = []): array
    {
        return $this->request('DELETE', $endpoint, ['json' => $data]);
    }

    /**
     * Send an HTTP request to the CREEM API.
     *
     * @param  string  $method  The HTTP method.
     * @param  string  $endpoint  The API endpoint.
     * @param  array<string, mixed>  $options  Guzzle request options.
     * @return array<string, mixed>
     *
     * @throws CreemApiException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $response = $this->http->request($method, $endpoint, $options);

            $body = (string) $response->getBody();

            if ($body === '') {
                return [];
            }

            return json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleException $e) {
            throw new CreemApiException(
                "CREEM API request failed: {$e->getMessage()}",
                $e->getCode(),
                $e
            );
        } catch (\JsonException $e) {
            throw new CreemApiException(
                "Failed to decode CREEM API response: {$e->getMessage()}",
                0,
                $e
            );
        }
    }

    /**
     * Handle a Guzzle client exception and throw the appropriate CREEM exception.
     *
     * @throws CreemApiException
     */
    protected function handleClientException(ClientException $e): never
    {
        $response = $e->getResponse();
        $statusCode = $response->getStatusCode();
        $body = json_decode((string) $response->getBody(), true) ?? [];

        $message = $body['message'] ?? $body['error'] ?? $e->getMessage();
        if (is_array($message)) {
            $message = implode(', ', $message);
        }

        $traceId = $body['trace_id'] ?? null;

        if ($statusCode === 429) {
            $retryAfter = $response->hasHeader('Retry-After')
                ? (int) $response->getHeaderLine('Retry-After')
                : null;

            throw new CreemRateLimitException($message, $statusCode, $e, $traceId, $retryAfter);
        }

        match ($statusCode) {
            401, 403 => throw new CreemAuthenticationException($message, $statusCode, $e, $traceId),
            default => throw new CreemApiException($message, $statusCode, $e, $traceId),
        };
    }

    /**
     * Get the underlying Guzzle HTTP client (for testing).
     */
    public function getHttpClient(): Client
    {
        return $this->http;
    }

    /**
     * Set a custom Guzzle HTTP client (for testing).
     */
    public function setHttpClient(Client $client): void
    {
        $this->http = $client;
    }
}
