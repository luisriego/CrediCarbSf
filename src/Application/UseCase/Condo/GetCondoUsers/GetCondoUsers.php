<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\GetCondoUsers
;

use App\Domain\Repository\UserRepositoryInterface;

class GetCondoUsers
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {
    }

    public function execute(string $condoId): array
    {
        $users = $this->userRepo->findAllByCondoId($condoId);

        return $users;
    }
}
