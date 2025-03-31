<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Domain\Event\DiscountCodeApplied;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Exception\ShoppingCart\InvalidDiscountException;
use App\Domain\Repository\DiscountRepositoryInterface;
use App\Domain\Repository\ShoppingCartRepositoryInterface;
use Psr\Log\LoggerInterface;

readonly class DiscountCodeAppliedHandler
{
    public function __construct(
        private ShoppingCartRepositoryInterface $cartRepository,
        private DiscountRepositoryInterface $discountRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws InvalidDiscountException
     */
    public function __invoke(DiscountCodeApplied $event): void
    {
        $this->logger->info('Applying discount code', [
            'cart_id' => $event->getCartId(),
            'discount_code' => $event->getDiscountCode(),
        ]);

        $cart = $this->cartRepository->find($event->getCartId());

        if ($cart === null) {
            $this->logger->error('Shopping cart not found', ['cart_id' => $event->getCartId()]);

            throw InvalidArgumentException::createFromMessage("A Shopping cart whit id {$event->getCartId()} does not exist");
        }

        try {
            $discount = $this->discountRepository->findOneByCodeOrFail($event->getDiscountCode());

            if (!$discount->isValid()) {
                throw InvalidDiscountException::createWithMessage('Discount code is not valid or expired');
            }

            $cart->applyValidatedDiscount($discount);

            $this->cartRepository->save($cart, true);

            $this->logger->info('Discount code applied successfully', [
                'cart_id' => $cart->getId(),
                'discount_code' => $event->getDiscountCode(),
                'new_total' => $cart->getTotal(),
            ]);
        } catch (InvalidDiscountException $e) {
            $this->logger->warning('Discount invalid', [
                'cart_id' => $cart->getId(),
                'discount_code' => $event->getDiscountCode(),
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
