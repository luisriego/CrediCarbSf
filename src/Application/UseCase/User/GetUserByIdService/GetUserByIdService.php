<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetUserByIdService;

use App\Application\UseCase\User\GetUserByIdService\Dto\GetUserByIdInputDto;
use App\Application\UseCase\User\GetUserByIdService\Dto\GetUserByIdOutputDto;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use PhpParser\Node\Expr\Array_;

class GetUserByIdService
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function handle(GetUserByIdInputDto $inputDto): GetUserByIdOutputDto
    {
        return GetUserByIdOutputDto::create($this->userRepository->findOneByIdOrFail($inputDto->id));
    }
}