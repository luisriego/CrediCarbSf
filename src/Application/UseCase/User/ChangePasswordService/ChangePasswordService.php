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

        if (!$this->passwordHasher->isPasswordValid($user, $dto->oldPassword)) {
            throw new \Symfony\Component\PasswordHasher\Exception\InvalidPasswordException('Invalid password.');
        }

        $user->setPassword($this->passwordHasher->hashPasswordForUser($user, $dto->newPassword));

        $user->markAsUpdated();

        $this->userRepository->save($user, true);

        return ChangePasswordOutputDto::createFromModel($user);
    }
}
