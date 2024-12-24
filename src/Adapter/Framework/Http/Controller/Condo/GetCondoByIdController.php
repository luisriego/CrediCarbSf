<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\GetCondoByIdRequestDto;
use App\Application\UseCase\Condo\GetCondoById\Dto\GetCondoByIdInputDto;
use App\Application\UseCase\Condo\GetCondoById\GetCondoById;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetCondoByIdController extends AbstractController
{
    public function __construct(
        private readonly GetCondoById $useCase
    ) {
    }

    #[Route('/{id}', name: 'get_condo_by_id', methods: ['GET'])]
    public function __invoke(GetCondoByIdRequestDto $request): Response
    {
        $responseDTO = $this->useCase->handle(GetCondoByIdInputDto::create($request->id));

        return $this->json($responseDTO);
    }
}
