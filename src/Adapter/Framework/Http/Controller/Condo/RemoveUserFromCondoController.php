<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\RemoveUserFromCondoRequestDto;
use App\Application\UseCase\Condo\RemoveUserFromCondo\Dto\RemoveUserFromCondoInputDto;
use App\Application\UseCase\Condo\RemoveUserFromCondo\RemoveUserFromCondo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RemoveUserFromCondoController extends AbstractController
{
    public function __construct(private readonly RemoveUserFromCondo $useCase)
    {
    }

    #[Route('/remove-user-from-condo', name: 'remove_user_from_condo', methods: ['PUT'])]
    public function __invoke(RemoveUserFromCondoRequestDto $request): Response
    {
        $inputDto = RemoveUserFromCondoInputDto::create($request->id, $request->userId);

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->condoData);
    }
}
