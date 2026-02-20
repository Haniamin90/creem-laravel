<?php

namespace Creem\Laravel\Tests\Unit;

use Creem\Laravel\Http\Middleware\VerifyCreemWebhook;
use PHPUnit\Framework\TestCase;

class WebhookVerificationTest extends TestCase
{
    public function test_verify_returns_true_for_valid_signature(): void
    {
        $secret = 'test_secret_123';
        $payload = '{"eventType":"checkout.completed","object":{}}';
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertTrue(
            VerifyCreemWebhook::verify($payload, $signature, $secret)
        );
    }

    public function test_verify_returns_false_for_invalid_signature(): void
    {
        $secret = 'test_secret_123';
        $payload = '{"eventType":"checkout.completed","object":{}}';
        $invalidSignature = 'invalid_signature_here';

        $this->assertFalse(
            VerifyCreemWebhook::verify($payload, $invalidSignature, $secret)
        );
    }

    public function test_verify_returns_false_for_tampered_payload(): void
    {
        $secret = 'test_secret_123';
        $originalPayload = '{"eventType":"checkout.completed","object":{}}';
        $signature = hash_hmac('sha256', $originalPayload, $secret);

        $tamperedPayload = '{"eventType":"checkout.completed","object":{"hacked":true}}';

        $this->assertFalse(
            VerifyCreemWebhook::verify($tamperedPayload, $signature, $secret)
        );
    }

    public function test_verify_returns_false_for_wrong_secret(): void
    {
        $secret = 'correct_secret';
        $wrongSecret = 'wrong_secret';
        $payload = '{"eventType":"checkout.completed"}';
        $signature = hash_hmac('sha256', $payload, $secret);

        $this->assertFalse(
            VerifyCreemWebhook::verify($payload, $signature, $wrongSecret)
        );
    }
}
