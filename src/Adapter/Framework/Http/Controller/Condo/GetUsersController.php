<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\API\Filter\UserFilter;
use App\Adapter\Framework\Http\Dto\User\GetUsersRequestDto;
use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\Condo\GetCondoUsers\GetCondoUsers;
use App\Application\UseCase\Condo\Search\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetUsersController extends AbstractController
{
    public function __construct(
        private readonly Search $useCase,
        private readonly GetCondoUsers $getCondoUsers
    ) {
    }

    #[Route('/{id}/users', name: 'get_condo_users', methods: ['GET'])]
    public function __invoke(GetUsersRequestDto $request, string $id): Response
    {
        $users = $this->getCondoUsers->execute($id);

        $this->denyAccessUnlessGranted(UserVoter::GET_USERS_CONDO, $users);

        $filter = new UserFilter(
            $request->page,
            $request->limit,
            $id,
            $request->sort,
            $request->order,
            $request->name
        );

        $output = $this->useCase->execute($filter);

        return $this->json($output->users);
    }
}
