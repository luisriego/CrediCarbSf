<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ActivateUser;

use App\Application\UseCase\User\ActivateUser\Dto\ActivateUserInputDto;
use App\Application\UseCase\User\ActivateUser\Dto\ActivateUserOutputDto;
use App\Domain\Exception\User\UserTokenInvalidException;
use App\Domain\Repository\UserRepositoryInterface;

class ActivateUser
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function handle(ActivateUserInputDto $inputDto): ActivateUserOutputDto
    {
        $user = $this->userRepository->findOneByIdOrFail($inputDto->id);

        if ($inputDto->token !== $user->getToken()) {
            throw UserTokenInvalidException::FromToken($inputDto->token);
        }

        $user->toggleActive();
        $user->setToken('');
        $user->markAsUpdated();

        $this->userRepository->save($user, true);

        return ActivateUserOutputDto::createFromModel($user);
    }
}
