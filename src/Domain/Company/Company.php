<?php

declare(strict_types=1);

namespace App\Domain\Company;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
#[ORM\Table(name: 'companies')]
#[ORM\UniqueConstraint(name: 'uniq_companies_inn', columns: ['inn'])]
final class Company
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    private string $id;

    #[ORM\Column(type: 'string', length: 12)]
    private string $inn;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status;

    #[ORM\Column(name: 'okved_code', type: 'string', length: 32, nullable: true)]
    private ?string $okvedCode;

    #[ORM\Column(name: 'checked_at', type: 'datetime_immutable')]
    private DateTimeImmutable $checkedAt;

    public function __construct(
        string $inn,
        string $name,
        string $status,
        ?string $okvedCode,
        DateTimeImmutable $checkedAt,
        ?string $id = null
    ) {
        $this->id = $id ?? Uuid::v7()->toRfc4122();
        $this->inn = $inn;
        $this->name = $name;
        $this->status = $status;
        $this->okvedCode = $okvedCode;
        $this->checkedAt = $checkedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getOkvedCode(): ?string
    {
        return $this->okvedCode;
    }

    public function getCheckedAt(): DateTimeImmutable
    {
        return $this->checkedAt;
    }

    public function isActive(): bool
    {
        return 'ACTIVE' === $this->status;
    }
}
