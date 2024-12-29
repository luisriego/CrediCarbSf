<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\ActivateUserRequestDto;
use App\Application\UseCase\User\ActivateUser\ActivateUser;
use App\Application\UseCase\User\ActivateUser\Dto\ActivateUserInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivateUserController extends AbstractController
{
    public function __construct(private readonly ActivateUser $useCase) {}

    #[Route('/activate', name: 'activate_user', methods: ['PUT'])]
    public function __invoke(ActivateUserRequestDto $request): Response
    {
        $inputDto = ActivateUserInputDto::create($request->id, $request->token);

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->userData);
    }
}
