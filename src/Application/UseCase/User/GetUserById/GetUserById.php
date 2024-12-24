<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetUserById;

use App\Application\UseCase\User\GetUserById\Dto\GetUserByIdInputDto;
use App\Application\UseCase\User\GetUserById\Dto\GetUserByIdOutputDto;
use App\Domain\Repository\UserRepositoryInterface;

class GetUserById
{
    public function __construct(
        private readonly UserRepositoryInterface $customerRepository
    ) {
    }

    public function handle(GetUserByIdInputDto $dto): GetUserByIdOutputDto
    {
        return GetUserByIdOutputDto::create($this->customerRepository->findOneByIdOrFail($dto->id));
    }
}
