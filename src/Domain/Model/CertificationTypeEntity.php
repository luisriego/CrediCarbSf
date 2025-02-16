<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Trait\IdentifierTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CertificationTypeEntity
{
    use IdentifierTrait;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 1500)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: CertificationAuthority::class, inversedBy: 'certificationTypes')]
    private CertificationAuthority $certificationAuthority;

    public function __construct(string $name, string $description)
    {
        $this->initializeId();
        $this->name = $name;
        $this->description = $description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCertificationAuthority(): CertificationAuthority
    {
        return $this->certificationAuthority;
    }

    public function setCertificationAuthority(CertificationAuthority $certificationAuthority): void
    {
        $this->certificationAuthority = $certificationAuthority;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'certificationAuthority' => $this->getCertificationAuthority()->toArray(),
        ];
    }
}
