<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Adapter\Framework\Http\Dto\ShoppingCart\CreateDiscountRequestDto;
use App\Application\UseCase\ShoppingCart\CreateDiscount\CreateDiscountService;
use App\Application\UseCase\ShoppingCart\CreateDiscount\Dto\CreateDiscountInputDto;
use App\Domain\Exception\ShoppingCart\DiscountAlreadyExistsException;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreateDiscountController extends AbstractController
{
    public function __construct(
        private readonly CreateDiscountService $createDiscountService,
    ) {}

    /**
     * @throws DiscountAlreadyExistsException|RandomException
     */
    #[Route('/api/discount', name: 'discount_create', methods: ['POST'])]
    #[IsGranted('ROLE_OPERATOR')]
    public function __invoke(CreateDiscountRequestDto $requestDto): Response
    {
        $responseDto = $this->createDiscountService->handle(
            CreateDiscountInputDto::create(
                $requestDto->creatorId,
                $requestDto->amount,
                $requestDto->expiresAt->format('Y-m-d H:i:s'),
            ),
        );

        return new JsonResponse($responseDto, Response::HTTP_CREATED);
    }
}
