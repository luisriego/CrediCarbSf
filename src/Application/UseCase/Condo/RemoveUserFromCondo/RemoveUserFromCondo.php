<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\RemoveUserFromCondo;

use App\Application\UseCase\Condo\RemoveUserFromCondo\Dto\RemoveUserFromCondoInputDto;
use App\Application\UseCase\Condo\RemoveUserFromCondo\Dto\RemoveUserFromCondoOutputDto;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Model\Condo;
use App\Domain\Model\User;
use App\Domain\Repository\CondoRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class RemoveUserFromCondo
{
    public function __construct(
        private readonly CondoRepositoryInterface $condoRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(RemoveUserFromCondoInputDto $dto): RemoveUserFromCondoOutputDto
    {
        if (null === $condo = $this->condoRepository->findOneByIdOrFail($dto->id)) {
            throw ResourceNotFoundException::createFromClassAndId(Condo::class, $dto->id);
        }

        if (null === $user = $this->userRepository->findOneByIdOrFail($dto->userId)) {
            throw ResourceNotFoundException::createFromClassAndId(User::class, $dto->userId);
        }

        $condo->removeUser($user);
        $condo->markAsUpdated();

        $this->condoRepository->save($condo, true);

        return RemoveUserFromCondoOutputDto::createFromModel($condo);
    }
}
