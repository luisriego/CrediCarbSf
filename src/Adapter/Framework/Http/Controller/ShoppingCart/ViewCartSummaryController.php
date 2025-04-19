<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Application\UseCase\ShoppingCart\ViewCartSummary\ViewCartSummaryService;
use App\Domain\Model\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

readonly class ViewCartSummaryController
{
    public function __construct(
        private ViewCartSummaryService $viewCartSummaryUseCase,
        private Security $security,
    ) {}

    #[Route('/api/v1/shopping-carts', name: 'shopping_cart_summary', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        /** @var User $actualUser */
        $actualUser = $this->security->getUser();

        $responseDto = $this->viewCartSummaryUseCase->handle($actualUser);

        return new JsonResponse($responseDto, Response::HTTP_OK);
    }
}
