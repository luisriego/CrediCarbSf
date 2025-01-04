<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\User\GetUserByIdService\Dto\GetUserByIdInputDto;
use App\Application\UseCase\User\GetUserByIdService\GetUserByIdService;
use App\Domain\Model\User;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GetUsersByCompanyController extends AbstractController
{
    // TODO: Implement the GetUsersByCompanyController
//    public function __construct(
//        private readonly GetUserByIdService $useCase,
//        private readonly UserRepositoryInterface $userRepository,
//    ) {}
//
//    #[Route('/api/users/company/{id}', name: 'get_user_by_company', methods: ['GET'])]
//    public function __invoke(string $id): Response
//    {
//        $inputDto = GetUserByIdInputDto::create($id);
//        /** @var User $userToReturn */
//        $userToReturn = $this->userRepository->findOneByIdOrFail($id);
//
//        $this->denyAccessUnlessGranted(UserVoter::GET_USER_BY_ID, $userToReturn);
//
//        $userReturned = $this->useCase->handle($inputDto);
//
//        return new JsonResponse(['user' => $userReturned->data], Response::HTTP_OK);
//    }
}
