<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ChangePasswordService;

use App\Application\UseCase\User\ChangePasswordService\Dto\ChangePasswordInputDto;
use App\Application\UseCase\User\ChangePasswordService\Dto\ChangePasswordOutputDto;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Security\PasswordHasherInterface;

class ChangePasswordService
{
    private const SETTER_PREFIX = 'set';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly PasswordHasherInterface $passwordHasher,
    ) {}

    public function handle(ChangePasswordInputDto $dto): ChangePasswordOutputDto
    {
        $user = $this->userRepository->findOneByIdOrFail($dto->id);

        $user->changePassword($dto->newPassword, $this->passwordHasher);

        $this->userRepository->save($user, true);

        return ChangePasswordOutputDto::createFromModel($user);
    }
}
