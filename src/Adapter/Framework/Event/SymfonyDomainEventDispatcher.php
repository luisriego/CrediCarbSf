<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Event;

use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\DomainEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class SymfonyDomainEventDispatcher implements DomainEventDispatcherInterface
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
    ) {}

    public function dispatch(DomainEventInterface $event): void
    {
//        var_dump('Dispatching event: ' . get_class($event)); // for debugging
        $this->eventDispatcher->dispatch($event);
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
}
