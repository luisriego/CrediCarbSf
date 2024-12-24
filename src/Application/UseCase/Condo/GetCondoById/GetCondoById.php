<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\GetCondoById;

use App\Application\UseCase\Condo\GetCondoById\Dto\GetCondoByIdInputDto;
use App\Application\UseCase\Condo\GetCondoById\Dto\GetCondoByIdOutputDto;
use App\Domain\Repository\CondoRepositoryInterface;

class GetCondoById
{
    public function __construct(
        private readonly CondoRepositoryInterface $customerRepository
    ) {
    }

    public function handle(GetCondoByIdInputDto $dto): GetCondoByIdOutputDto
    {
        return GetCondoByIdOutputDto::create($this->customerRepository->findOneByIdOrFail($dto->id));
    }
}
