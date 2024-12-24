<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\CreateCondoRequestDto;
use App\Application\UseCase\Condo\CreateCondo\CreateCondo;
use App\Application\UseCase\Condo\CreateCondo\Dto\CreateCondoInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateCondoController extends AbstractController
{
    public function __construct(
        private readonly CreateCondo $createCondo)
    {
    }

    #[Route('/create', name: 'condo_create', methods: ['POST'])]
    public function __invoke(CreateCondoRequestDto $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $responseDto = $this->createCondo->handle(
            CreateCondoInputDto::create(
                $request->taxpayer,
                $request->fantasyName)
        );

        return $this->json(['condoId' => $responseDto->id], Response::HTTP_CREATED);
    }
}
