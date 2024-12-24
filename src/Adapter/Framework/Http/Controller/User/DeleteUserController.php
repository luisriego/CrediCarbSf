<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\DeleteUserRequestDto;
use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\User\DeleteUser\DeleteUser;
use App\Application\UseCase\User\DeleteUser\Dto\DeleteUserInputDto;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteUserController extends AbstractController
{
    public function __construct(
        private readonly DeleteUser $useCase,
        private readonly UserRepositoryInterface $userRepo,
    ) {
    }

    #[Route('/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function __invoke(DeleteUserRequestDto $requestDto): Response
    {
        $userToDelete = $this->userRepo->findOneByIdOrFail($requestDto->id);
        $this->denyAccessUnlessGranted(UserVoter::DELETE_USER, $userToDelete);

        $this->useCase->handle(DeleteUserInputDto::create($requestDto->id));

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
