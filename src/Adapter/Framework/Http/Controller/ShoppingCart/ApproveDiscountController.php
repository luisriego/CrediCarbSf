<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Adapter\Framework\Http\Dto\ShoppingCart\ApproveDiscountRequestDto;
use App\Adapter\Framework\Security\Voter\DiscountVoter;
use App\Application\UseCase\ShoppingCart\ApproveDiscount\ApproveDiscountService;
use App\Application\UseCase\ShoppingCart\ApproveDiscount\Dto\ApproveDiscountInputDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApproveDiscountController extends AbstractController
{
    public function __construct(private readonly ApproveDiscountService $approveDiscountService) {}

    #[Route('/api/v1/discounts/approve', name: 'discount_approve', methods: ['POST'])]
    public function __invoke(ApproveDiscountRequestDto $requestDto): Response
    {
        $this->denyAccessUnlessGranted(DiscountVoter::APPROVE_DISCOUNT, $requestDto->discountId);

        $this->approveDiscountService->handle(
            ApproveDiscountInputDto::create(
                $requestDto->discountId,
            ),
        );

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
