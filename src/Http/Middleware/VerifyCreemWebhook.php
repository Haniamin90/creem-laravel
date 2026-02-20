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
            abort(403, 'CREEM webhook secret is not configured.');
        }

        $signature = $request->header('creem-signature');

        if (empty($signature)) {
            abort(403, 'Missing creem-signature header.');
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSignature, $signature)) {
            abort(403, 'Invalid webhook signature.');
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
