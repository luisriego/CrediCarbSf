<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Common\CertificationType;
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

    public function __construct(string $name, string $description)
    {
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
}