<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use Doctrine\ORM\Mapping as ORM;

trait IdentifierTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36, options: ['fixed' => true])]
    protected readonly string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
