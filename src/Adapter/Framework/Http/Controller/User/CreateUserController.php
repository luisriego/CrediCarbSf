<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\User;

use App\Adapter\Framework\Http\Dto\User\CreateUserRequestDto;
use App\Application\UseCase\User\CreateUser\CreateUser;
use App\Application\UseCase\User\CreateUser\Dto\CreateUserInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateUserController extends AbstractController
{
    public function __construct(private readonly CreateUser $createUserService) {}

    #[Route('/register', name: 'user_create', methods: ['POST'])]
    public function __invoke(CreateUserRequestDto $request): Response
    {
        $responseDto = $this->createUserService->handle(
            CreateUserInputDto::create(
                $request->id,
                $request->name,
                $request->email,
                $request->password,
                $request->age,
            ),
        );

        return $this->json(['userId' => $responseDto->id], Response::HTTP_CREATED);
    }
}
