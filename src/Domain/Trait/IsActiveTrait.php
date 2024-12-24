<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use Doctrine\ORM\Mapping as ORM;

trait IsActiveTrait
{
    #[ORM\Column(type: 'boolean')]
    protected bool $isActive = false;

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function toggleActive(): void
    {
        $this->isActive = !$this->isActive;
    }
}
