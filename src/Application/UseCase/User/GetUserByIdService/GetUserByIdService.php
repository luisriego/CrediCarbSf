<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetUserByIdService;

use App\Application\UseCase\User\GetUserByIdService\Dto\GetUserByIdInputDto;
use App\Application\UseCase\User\GetUserByIdService\Dto\GetUserByIdOutputDto;
use App\Domain\Repository\UserRepositoryInterface;

class GetUserByIdService
{
    public function __construct(private readonly UserRepositoryInterface $userRepository) {}

    public function handle(GetUserByIdInputDto $inputDto): GetUserByIdOutputDto
    {
        return GetUserByIdOutputDto::create($this->userRepository->findOneByIdOrFail($inputDto->id));
    }
}
