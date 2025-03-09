<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use App\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;

trait CreatedByTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected User $createdBy;

    public function createdBy(): User
    {
        return $this->createdBy;
    }

    protected function setCreator(User $user): void
    {
        $this->createdBy = $user;
    }
}
