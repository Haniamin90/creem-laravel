<?php

namespace Creem\Laravel\Http\Middleware;

use Closure;
use Creem\Laravel\Exceptions\WebhookVerificationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCreemWebhook
{
    /**
     * Verify the CREEM webhook signature.
     *
     * @throws WebhookVerificationException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secret = config('creem.webhook_secret');

        if (empty($secret)) {
            throw new WebhookVerificationException(
                'CREEM webhook secret is not configured. Set CREEM_WEBHOOK_SECRET in your .env file.'
            );
        }

        $signature = $request->header('creem-signature');

        if (empty($signature)) {
            throw new WebhookVerificationException(
                'Missing creem-signature header.'
            );
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            throw new WebhookVerificationException(
                'Invalid webhook signature.'
            );
        }

        return $next($request);
    }

    /**
     * Verify a webhook signature manually.
     *
     * @param  string  $payload  The raw request body.
     * @param  string  $signature  The signature from the creem-signature header.
     * @param  string  $secret  The webhook signing secret.
     */
    public static function verify(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }
}
