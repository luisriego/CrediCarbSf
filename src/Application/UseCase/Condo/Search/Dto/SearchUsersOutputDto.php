<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\Search\Dto;

use App\Adapter\Framework\Http\API\Response\PaginatedResponse;
use App\Domain\Model\User;

class SearchUsersOutputDto
{
    private function __construct(
        public readonly array $users
    ) {
    }

    public static function createFromPaginateResponse(PaginatedResponse $paginatedResponse): self
    {
        $items = array_map(function (User $user): array {
            return [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }, $paginatedResponse->getItems());

        $response['items'] = $items;
        $response['meta'] = $paginatedResponse->getMeta();

        return new SearchUsersOutputDto($response);
    }
}
