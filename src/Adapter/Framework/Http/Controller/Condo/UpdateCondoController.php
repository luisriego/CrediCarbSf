<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\UpdateCondoRequestDto;
use App\Application\UseCase\Condo\UpdateCondo\Dto\UpdateCondoInputDto;
use App\Application\UseCase\Condo\UpdateCondo\UpdateCondo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCondoController extends AbstractController
{
    public function __construct(private readonly UpdateCondo $useCase)
    {
    }

    #[Route('/{id}', name: 'update_condo', methods: ['PATCH'])]
    public function __invoke(UpdateCondoRequestDto $request, string $id): Response
    {
        $inputDto = UpdateCondoInputDto::create($id, $request->taxpayer, $request->fantasyName, $request->keys);

        $responseDto = $this->useCase->handle($inputDto);

        return $this->json($responseDto->condoData);
    }
}
