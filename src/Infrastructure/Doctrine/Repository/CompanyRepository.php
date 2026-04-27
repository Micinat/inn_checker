<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Company\Company;
use App\Domain\Company\Repository\CompanyRepositoryInterface;
use App\Infrastructure\Doctrine\Exception\DatabaseWriteException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Types;

final class CompanyRepository implements CompanyRepositoryInterface
{
    public function __construct(private Connection $connection)
    {
    }

    public function upsert(Company $company): void
    {
        $sql = <<<'SQL'
            INSERT INTO companies (id, inn, name, status, okved_code, checked_at)
            VALUES (:id, :inn, :name, :status, :okved_code, :checked_at)
            ON DUPLICATE KEY UPDATE
              name = VALUES(name),
              status = VALUES(status),
              okved_code = VALUES(okved_code),
              checked_at = VALUES(checked_at)
            SQL;

        try {
            $this->connection->executeStatement(
                $sql,
                [
                    'id' => $company->getId(),
                    'inn' => $company->getInn(),
                    'name' => $company->getName(),
                    'status' => $company->getStatus(),
                    'okved_code' => $company->getOkvedCode(),
                    'checked_at' => $company->getCheckedAt(),
                ],
                [
                    'id' => Types::STRING,
                    'inn' => Types::STRING,
                    'name' => Types::STRING,
                    'status' => Types::STRING,
                    'okved_code' => Types::STRING,
                    'checked_at' => Types::DATETIME_IMMUTABLE,
                ]
            );
        } catch (Exception $exception) {
            throw DatabaseWriteException::fromThrowable($exception);
        }
    }
}
