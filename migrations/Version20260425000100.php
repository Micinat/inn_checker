<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260425000100 extends AbstractMigration
{
    public function isTransactional(): bool
    {
        return false;
    }

    public function getDescription(): string
    {
        return 'Create companies table for INN lookup snapshots.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            <<<'SQL'
        CREATE TABLE companies (
            id VARCHAR(36) NOT NULL,
            inn VARCHAR(12) NOT NULL,
            name VARCHAR(255) NOT NULL,
            status VARCHAR(32) NOT NULL,
            okved_code VARCHAR(32) DEFAULT NULL,
            checked_at DATETIME NOT NULL,
            UNIQUE INDEX UNIQ_COMPANIES_INN (inn),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE companies');
    }
}
