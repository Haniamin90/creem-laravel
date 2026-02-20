<?php

namespace Creem\Laravel\Exceptions;

use Exception;
use Throwable;

class CreemApiException extends Exception
{
    protected ?string $traceId;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?string $traceId = null
    ) {
        $this->traceId = $traceId;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the CREEM API trace ID for debugging.
     */
    public function getTraceId(): ?string
    {
        return $this->traceId;
    }
}
