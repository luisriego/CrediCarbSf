<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\AddUserToCondo;

use App\Application\UseCase\Condo\AddUserToCondo\Dto\AddUserToCondoInputDto;
use App\Application\UseCase\Condo\AddUserToCondo\Dto\AddUserToCondoOutputDto;
use App\Domain\Exception\ResourceNotFoundException;
use App\Domain\Exception\User\UserAlreadyIsMemberException;
use App\Domain\Model\Condo;
use App\Domain\Model\User;
use App\Domain\Repository\CondoRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

class AddUserToCondo
{
    public function __construct(
        private readonly CondoRepositoryInterface $condoRepository,
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function handle(AddUserToCondoInputDto $dto): AddUserToCondoOutputDto
    {
        if (null === $condo = $this->condoRepository->findOneByIdOrFail($dto->id)) {
            throw ResourceNotFoundException::createFromClassAndId(Condo::class, $dto->id);
        }

        if (null === $user = $this->userRepository->findOneByIdOrFail($dto->userId)) {
            throw ResourceNotFoundException::createFromClassAndId(User::class, $dto->userId);
        }

        if ($user->getCondo() && $condo->getId() === $user->getCondo()->getId()) {
            throw UserAlreadyIsMemberException::fromId($user->getId());
        }

        $condo->addUser($user);
        $condo->markAsUpdated();

        $this->condoRepository->save($condo, true);

        return AddUserToCondoOutputDto::createFromModel($condo);
    }
}
