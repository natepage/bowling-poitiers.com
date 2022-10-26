<?php
declare(strict_types=1);

namespace App\Entity;

use Carbon\CarbonImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractEntity
{
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected ?CarbonImmutable $createdAt = null;

    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    protected string $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    protected ?CarbonImmutable $updatedAt = null;

    public function getCreatedAt(): ?CarbonImmutable
    {
        return $this->createdAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUpdatedAt(): ?CarbonImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps(): void
    {
        $dateTime = CarbonImmutable::now();

        if (isset($this->createdAt) === false) {
            $this->createdAt = $dateTime;
        }

        $this->updatedAt = $dateTime;
    }
}
