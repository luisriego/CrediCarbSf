<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Repository\CertificationRepositoryInterface;
use App\Domain\Trait\IdentifierTrait;
use App\Domain\Trait\IsActiveTrait;
use App\Domain\Trait\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepositoryInterface::class)]
#[ORM\HasLifecycleCallbacks]
class Certification
{
    use IdentifierTrait;
    use TimestampableTrait;
    use IsActiveTrait;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: CertificationTypeEntity::class, inversedBy: 'certificationsType')]
    private ?CertificationTypeEntity $type;

    #[ORM\ManyToOne(targetEntity: CertificationAuthority::class, inversedBy: 'certifications')]
    private ?CertificationAuthority $authority;

    public function __construct(
        string $name,
        string $description,
        CertificationTypeEntity $type,
        CertificationAuthority $authority,
    ) {
        $this->initializeId();
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->authority = $authority;
        $this->initializeCreatedOn();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): ?CertificationTypeEntity
    {
        return $this->type;
    }

    public function setType(?CertificationTypeEntity $type): void
    {
        $this->type = $type;
    }

    public function getAuthority(): ?CertificationAuthority
    {
        return $this->authority;
    }

    public function setAuthority(?CertificationAuthority $authority): void
    {
        $this->authority = $authority;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'type' => $this->getType()->toArray(),
            'authority' => $this->getAuthority()->toArray(),
        ];
    }
}
