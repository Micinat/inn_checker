<?php

declare(strict_types=1);

namespace App\Domain\Company\Exception;

use DomainException;

final class CompanyNotFoundException extends DomainException
{
    public static function forInn(string $inn): self
    {
        return new self(sprintf('Company with INN "%s" was not found.', $inn));
    }
}
