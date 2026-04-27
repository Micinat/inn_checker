<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Exception;

use RuntimeException;
use Throwable;

final class DatabaseWriteException extends RuntimeException
{
    public static function fromThrowable(Throwable $throwable): self
    {
        return new self('Failed to persist company data.', previous: $throwable);
    }
}
