<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use App\Domain\ValueObjects\Uuid;
use Doctrine\ORM\Mapping as ORM;

trait IdentifierTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, options: ['fixed' => true])]
    protected readonly string $id;

    public function id(): ?string
    {
        if (!isset($this->id)) {
            $this->initializeId();
        }

        return $this->id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    private function initializeId(): void
    {
        if (!isset($this->id)) {
            $this->id = Uuid::random()->value();
        }
    }
}
