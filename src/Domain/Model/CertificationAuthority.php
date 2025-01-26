<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\ValueObjects\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationAuthorityRepository::class)]
#[ORM\HasLifecycleCallbacks]
class CertificationAuthority
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    private string $name;

    private string $website;

    public function __construct(
        string $name,
        string $website
    ) {
        $this->id = Uuid::random()->value();
        $this->name = $name;
        $this->website = $website;
        $this->isActive = true;
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create(
        string $name,
        string $website
    ): self {
        return new static(
            $name,
            $website
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getWebsite(): string
    {
        return $this->website;
    }

    public function setWebsite(string $website): void
    {
        $this->website = $website;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website' => $this->website,
            'isActive' => $this->isActive,
            'createdOn' => $this->createdOn->format('Y-m-d H:i:s'),
        ];
    }
}