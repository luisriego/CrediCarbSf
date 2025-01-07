<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\GetAllUsersService;

use App\Domain\Repository\UserRepositoryInterface;

use function array_map;

readonly class GetAllUsersService
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(): array
    {
        $users = $this->userRepository->findAll();

        $result = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'age' => $user->getAge(),
                'company' => $user->getCompany(),
            ];
        }, $users);

        return $result;
    }
}
