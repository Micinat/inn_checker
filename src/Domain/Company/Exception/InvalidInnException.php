<?php

declare(strict_types=1);

namespace App\Domain\Company\Exception;

use DomainException;

final class InvalidInnException extends DomainException
{
    public static function invalidFormat(): self
    {
        return new self('INN must contain 10 or 12 digits.');
    }

    public static function invalidChecksum(): self
    {
        return new self('INN checksum is invalid.');
    }
}
