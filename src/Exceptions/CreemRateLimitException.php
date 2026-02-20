<?php

namespace Creem\Laravel\Exceptions;

use Throwable;

class CreemRateLimitException extends CreemApiException
{
    protected ?int $retryAfter;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?string $traceId = null,
        ?int $retryAfter = null
    ) {
        $this->retryAfter = $retryAfter;
        parent::__construct($message, $code, $previous, $traceId);
    }

    /**
     * Get the number of seconds to wait before retrying.
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
