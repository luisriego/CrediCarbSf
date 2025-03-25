<?php

declare(strict_types=1);

namespace App\Adapter\Framework\EventListener;

use App\Domain\Event\DiscountCodeApplied;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

use function sprintf;

#[AsEventListener]
readonly class DiscountCodeAppliedListener
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(DiscountCodeApplied $event): void
    {
        $this->logger->info(sprintf(
            'Código de descuento "%s" aplicado al carrito %s con un descuento de %s',
            $event->discountCode(),
            $event->cartId(),
            $event->discountAmount(),
        ));

        // Aquí podrías añadir más lógica como:
        // - Actualizar estadísticas de uso de códigos de descuento
        // - Enviar notificaciones
        // - etc.
    }
}
