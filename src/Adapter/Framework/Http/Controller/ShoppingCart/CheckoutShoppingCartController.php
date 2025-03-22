<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\ShoppingCart;

use App\Application\UseCase\ShoppingCart\CheckoutShoppingCart\CheckoutShoppingCartService;
use App\Application\UseCase\User\UserFinder\UserFinder;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Exception\ShoppingCart\ShoppingCartWorkflowException;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CheckoutShoppingCartController extends AbstractController
{
    public function __construct(
        private readonly CheckoutShoppingCartService $service,
        private readonly ShoppingCartRepositoryInterface $repository,
        private readonly UserFinder $userFinder,
    ) {}

    /**
     * @throws InvalidDiscountException
     * @throws ShoppingCartWorkflowException
     */
    #[Route('/api/shopping-cart/checkout', name: 'checkout_shopping_cart', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(): Response
    {
        $user = $this->userFinder->getCurrentUser();
        $cart = $this->repository->findOneByOwnerIdOrFail($user->getCompany()->id());

        if (!$this->isGranted('modify', $cart)) {
            throw $this->createAccessDeniedException('Access Denied.');
        }

        $response = $this->service->handle($user, $cart);

        return new JsonResponse($response->toArray(), Response::HTTP_OK);
    }
}
