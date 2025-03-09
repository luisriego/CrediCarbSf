<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use App\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;

trait ApprovedByTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected User $approvedBy;

    public function approvedBy(): User
    {
        return $this->approvedBy;
    }

    protected function approve(User $user): void
    {
        $this->approvedBy = $user;
    }
}
