<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\ActivateUserRequestDto;
use App\Application\UseCase\User\ActivateUser\ActivateUser;
use App\Application\UseCase\User\ActivateUser\Dto\ActivateUserInputDto;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

readonly class ActivateUserController
{
    public function __construct(private ActivateUser $useCase) {}

    #[Route('/api/user/activate', name: 'activate_user', methods: ['PUT'])]
    public function __invoke(ActivateUserRequestDto $request): Response
    {
        $inputDto = ActivateUserInputDto::create($request->id, $request->token);

        $responseDto = $this->useCase->handle($inputDto);

        return new JsonResponse($responseDto->userData);
    }
}
