<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UpdateUSer;

use App\Application\UseCase\User\UpdateUSer\Dto\UpdateUserInputDto;
use App\Application\UseCase\User\UpdateUSer\Dto\UpdateUserOutputDto;
use App\Domain\Repository\UserRepositoryInterface;

class UpdateUser
{
    private const SETTER_PREFIX = 'set';

    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(UpdateUserInputDto $dto): UpdateUserOutputDto
    {
        $user = $this->userRepository->findOneByIdOrFail($dto->id);

        foreach ($dto->paramsToUpdate as $param) {
            $user->{\sprintf('%s%s', self::SETTER_PREFIX, \ucfirst($param))}($dto->{$param});
        }
        $user->markAsUpdated();

        $this->userRepository->save($user, true);

        return UpdateUserOutputDto::createFromModel($user);
    }
}
