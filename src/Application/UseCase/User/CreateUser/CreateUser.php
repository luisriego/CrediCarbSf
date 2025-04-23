<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\CreateUser;

use App\Application\UseCase\User\CreateUser\Dto\CreateUserInputDto;
use App\Application\UseCase\User\CreateUser\Dto\CreateUserOutputDto;
use App\Domain\Exception\User\UserAlreadyExistsException;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Security\PasswordHasherInterface;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\Password;
use App\Domain\ValueObject\UserId;
use App\Domain\ValueObject\UserName;

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
            UserId::fromString($inputDto->id),
            UserName::fromString($inputDto->name),
            Email::fromString($inputDto->email),
            Password::fromString($inputDto->password),
        );

        //        $password = $this->passwordHasher->hashPasswordForUser($user, $inputDto->password);
        $user->setPassword($inputDto->password, $this->passwordHasher);

        $this->repository->save($user, true);

        return new CreateUserOutputDto((string) $user->getId());
    }
}
