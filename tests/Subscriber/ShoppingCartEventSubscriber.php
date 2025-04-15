<?php

declare(strict_types=1);

namespace App\Tests\Subscriber;

use App\Domain\Event\ShoppingCartCheckedOut;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ShoppingCartEventSubscriber implements EventSubscriberInterface
{
    private int $eventCount = 0;

    public static function getSubscribedEvents(): array
    {
        return [
            ShoppingCartCheckedOut::class => 'onShoppingCartCheckedOut',
        ];
    }

    public function onShoppingCartCheckedOut(ShoppingCartCheckedOut $event): void
    {
        $this->eventCount++;
    }

    public function getEventCount(): int
    {
        return $this->eventCount;
    }
}