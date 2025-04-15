<?php

declare(strict_types=1);

namespace App\Adapter\Framework\EventListener;

use App\Domain\Event\ShoppingCartCheckedOut;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use function sprintf;

#[AsEventListener]
readonly class ShoppingCartCheckedOutListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ShoppingCartCheckedOut $event): void
    {
        $this->logger->info(sprintf(
            'Carrito %s finalizado con un total de %s y impuestos de %s',
            $event->cartId(),
            $event->total(),
            $event->tax(),
        ));

        // Aquí podrías añadir más lógica como:
        // - Enviar email de confirmación
        // - Actualizar inventario
        // - Integrar con sistemas de pago
        // - etc.
    }
}
