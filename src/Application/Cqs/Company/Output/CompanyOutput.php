<?php

declare(strict_types=1);

namespace App\Application\Cqs\Company\Output;

use App\Domain\Company\Company;

final class CompanyOutput
{
    public function __construct(
        public readonly string $inn,
        public readonly string $name,
        public readonly string $status,
        public readonly bool $isActive,
        public readonly ?string $okvedCode,
        public readonly string $checkedAt
    ) {
    }

    public static function fromCompany(Company $company): self
    {
        return new self(
            $company->getInn(),
            $company->getName(),
            $company->getStatus(),
            $company->isActive(),
            $company->getOkvedCode(),
            $company->getCheckedAt()->format(DATE_ATOM)
        );
    }

    public function toArray(): array
    {
        return [
            'inn' => $this->inn,
            'name' => $this->name,
            'status' => $this->status,
            'isActive' => $this->isActive,
            'okvedCode' => $this->okvedCode,
            'checkedAt' => $this->checkedAt,
        ];
    }
}
