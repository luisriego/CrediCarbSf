<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\Search
;

use App\Adapter\Framework\Http\API\Filter\UserFilter;
use App\Application\UseCase\Condo\Search\Dto\SearchUsersOutputDto;
use App\Domain\Repository\UserRepositoryInterface;

class Search
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {
    }

    public function execute(UserFilter $filter): SearchUsersOutputDto
    {
        $paginatedResponse = $this->userRepo->search($filter);
//        $users = $this->userRepo->findAllByCondoId($condoId);

        return SearchUsersOutputDto::createFromPaginateResponse($paginatedResponse);
    }
}
