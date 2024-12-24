<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\DeleteUser;

use App\Application\UseCase\User\DeleteUser\Dto\DeleteUserInputDto;
use App\Domain\Repository\UserRepositoryInterface;

class DeleteUser
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(DeleteUserInputDto $dto): void
    {
        $user = $this->userRepository->findOneByIdOrFail($dto->id);

        $this->userRepository->remove($user, true);
    }
}
