<?php

declare(strict_types=1);

namespace App\Application\Cqs\Company\Query;

use App\Application\Cqs\Company\Output\CompanyOutput;
use App\Domain\Company\Company;
use App\Domain\Company\Exception\CompanyNotFoundException;
use App\Domain\Company\Exception\InvalidInnException;
use App\Domain\Company\Repository\CompanyRepositoryInterface;
use App\Infrastructure\DaData\DaDataClient;
use DateTimeImmutable;
use DateTimeZone;

final class GetCompanyByInnQuery
{
    public function __construct(
        private CompanyRepositoryInterface $companyRepository,
        private DaDataClient $daDataClient
    ) {
    }

    public function execute(string $inn): CompanyOutput
    {
        $this->assertInnIsValid($inn);

        $suggestion = $this->daDataClient->findCompanyByInn($inn);

        if (null === $suggestion) {
            throw CompanyNotFoundException::forInn($inn);
        }

        $data = $suggestion['data'];
        $checkedAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $company = new Company(
            $inn,
            (string)$suggestion['value'],
            (string)$data['state']['status'],
            $this->resolveOkvedCode($data),
            $checkedAt
        );

        $this->companyRepository->upsert($company);

        return CompanyOutput::fromCompany($company);
    }

    private function assertInnIsValid(string $inn): void
    {
        if (!ctype_digit($inn) || !in_array(strlen($inn), [10, 12], true)) {
            throw InvalidInnException::invalidFormat();
        }

        if (!$this->isInnChecksumValid($inn)) {
            throw InvalidInnException::invalidChecksum();
        }
    }

    private function isInnChecksumValid(string $inn): bool
    {
        if (strlen($inn) === 10) {
            return $this->calculateChecksum($inn, [2, 4, 10, 3, 5, 9, 4, 6, 8]) === (int)$inn[9];
        }

        $firstChecksum = $this->calculateChecksum($inn, [7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);
        $secondChecksum = $this->calculateChecksum($inn, [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8]);

        return $firstChecksum === (int)$inn[10] && $secondChecksum === (int)$inn[11];
    }

    /**
     * @param list<int> $coefficients
     */
    private function calculateChecksum(string $inn, array $coefficients): int
    {
        $sum = 0;

        foreach ($coefficients as $index => $coefficient) {
            $sum += ((int)$inn[$index]) * $coefficient;
        }

        return ($sum % 11) % 10;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function resolveOkvedCode(array $data): ?string
    {
        if (!isset($data['okved']) || null === $data['okved'] || '' === (string)$data['okved']) {
            return null;
        }

        return (string)$data['okved'];
    }
}
