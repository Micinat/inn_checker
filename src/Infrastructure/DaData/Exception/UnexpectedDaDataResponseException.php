<?php

declare(strict_types=1);

namespace App\Infrastructure\DaData\Exception;

use RuntimeException;
use Throwable;

final class UnexpectedDaDataResponseException extends RuntimeException
{
    public static function becausePayloadIsInvalid(): self
    {
        return new self('DaData service is unavailable.');
    }

    public static function fromThrowable(Throwable $throwable): self
    {
        return new self('DaData service is unavailable.', previous: $throwable);
    }
}
