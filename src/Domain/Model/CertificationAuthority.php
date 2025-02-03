<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\CertificationAuthorityRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use App\Domain\ValueObjects\Uuid;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationAuthorityRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class CertificationAuthority
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\Column(type: 'string', length: 14, options: ['fixed' => true])]
    private ?string $taxpayer = '';

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private ?string $website;

    public function __construct(
        string $taxpayer,
        string $name,
        string $website,
    ) {
        $this->id = Uuid::random()->value();
        $this->taxpayer = $taxpayer;
        $this->name = $name;
        $this->website = $website;
        $this->isActive = true;
        $this->createdOn = new DateTimeImmutable();
    }

    public static function create(
        string $taxpayer,
        string $name,
        string $website,
    ): self {
        return new static(
            $taxpayer,
            $name,
            $website,
        );
    }

    public function getTaxpayer(): ?string
    {
        return $this->taxpayer;
    }

    public function setTaxpayer(?string $taxpayer): void
    {
        $this->taxpayer = $taxpayer;
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
            'taxpayer' => $this->taxpayer,
            'name' => $this->name,
            'website' => $this->website,
            'createdOn' => $this->createdOn->format('Y-m-d H:i:s'),
            'updatedOn' => $this->updatedOn?->format('Y-m-d H:i:s'),
        ];
    }
}
