<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\CreateUser;

use App\Application\UseCase\User\CreateUser\Dto\CreateUserInputDto;
use App\Application\UseCase\User\CreateUser\Dto\CreateUserOutputDto;
use App\Domain\Exception\User\UserAlreadyExistsException;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Security\PasswordHasherInterface;

readonly class CreateUser
{
    public function __construct(
        private UserRepositoryInterface $repository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function handle(CreateUserInputDto $inputDto): CreateUserOutputDto
    {
        if (null !== $this->repository->findOneByEmail($inputDto->email)) {
            throw UserAlreadyExistsException::createFromEmail($inputDto->email);
        }

        $user = User::create(
            $inputDto->name,
            $inputDto->email,
            $inputDto->password,
        );

        //        $password = $this->passwordHasher->hashPasswordForUser($user, $inputDto->password);
        $user->setPassword($inputDto->password, $this->passwordHasher);

        $this->repository->save($user, true);

        return new CreateUserOutputDto((string) $user->getId());
    }
}
