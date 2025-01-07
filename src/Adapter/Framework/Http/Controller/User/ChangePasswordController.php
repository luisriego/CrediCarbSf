<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\ChangePasswordRequestDto;
use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\User\ChangePasswordService\ChangePasswordService;
use App\Application\UseCase\User\ChangePasswordService\Dto\ChangePasswordInputDto;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
{
    public function __construct(
        private readonly ChangePasswordService $useCase,
        private readonly UserRepositoryInterface $userRepo,
    ) {}

    #[Route('/api/user/change-password/{id}', name: 'change_user_password', methods: ['PATCH'])]
    public function __invoke(ChangePasswordRequestDto $request, string $id): Response
    {
        $inputDto = ChangePasswordInputDto::create(
            $id,
            $request->oldPassword,
            $request->newPassword,
        );

        /** @var User $userToUpdate */
        $userToUpdate = $this->userRepo->findOneByIdOrFail($id);

        $this->denyAccessUnlessGranted(UserVoter::UPDATE_USER, $userToUpdate);

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->userData);
    }
}
