<?php

declare(strict_types=1);

namespace App\Infrastructure\DaData\Exception;

use RuntimeException;
use Throwable;

final class DaDataUnavailableException extends RuntimeException
{
    public static function fromThrowable(Throwable $throwable): self
    {
        return new self('DaData service is unavailable.', previous: $throwable);
    }

    public static function becauseOfStatusCode(int $statusCode): self
    {
        return new self(sprintf('DaData service is unavailable. HTTP status: %d.', $statusCode));
    }
}
