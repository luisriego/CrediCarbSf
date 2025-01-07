<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\GetAllRequestDto;
use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\User\GetAllUsersService\GetAllUsersService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetUsersController extends AbstractController
{
    public function __construct(
        private readonly GetAllUsersService $useCase,
    ) {}

    #[Route('/api/user/all', name: 'get_users', methods: ['GET'])]
    public function __invoke(GetAllRequestDto $request): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::GET_ALL_USERS);

        $responseDto = $this->useCase->handle();

        return new JsonResponse(['users' => $responseDto], Response::HTTP_OK);
    }
}
