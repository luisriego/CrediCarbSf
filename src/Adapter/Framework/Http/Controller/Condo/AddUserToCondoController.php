<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\AddUserToCondoRequestDto;
use App\Adapter\Framework\Security\Voter\UserVoter;
use App\Application\UseCase\Condo\AddUserToCondo\AddUserToCondo;
use App\Application\UseCase\Condo\AddUserToCondo\Dto\AddUserToCondoInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddUserToCondoController extends AbstractController
{
    public function __construct(private readonly AddUserToCondo $useCase)
    {
    }

    #[Route('/add-user-to-condo', name: 'add_user_to_condo', methods: ['PUT'])]
    public function __invoke(AddUserToCondoRequestDto $request): Response
    {
        $inputDto = AddUserToCondoInputDto::create($request->id, $request->userId);

        $this->denyAccessUnlessGranted(UserVoter::ADD_NEW_USER, $inputDto->id);

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->condoData);
    }
}
