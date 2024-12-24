<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Condo;

use App\Adapter\Framework\Http\Dto\Condo\DeleteCondoRequestDto;
use App\Adapter\Framework\Security\Voter\CondoVoter;
use App\Application\UseCase\Condo\DeleteCondo\DeleteCondo;
use App\Application\UseCase\Condo\DeleteCondo\Dto\DeleteCondoInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteCondoController extends AbstractController
{
    public function __construct(private readonly DeleteCondo $useCase)
    {
    }

    #[Route('/{id}', name: 'delete_condo', methods: ['DELETE'])]
    public function __invoke(DeleteCondoRequestDto $requestDto): Response
    {
        $this->denyAccessUnlessGranted(CondoVoter::DELETE_CONDO, $requestDto->id);

        $this->useCase->handle(DeleteCondoInputDto::create($requestDto->id));

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
