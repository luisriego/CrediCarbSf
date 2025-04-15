<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Event;

use App\Domain\Event\DomainEventDispatcherInterface;
use App\Domain\Event\DomainEventInterface;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class InMemoryDomainEventDispatcher implements DomainEventDispatcherInterface
{
    /** @var array<string, array<callable>> */
    private array $handlers = [];
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function registerHandler(string $eventClassName, callable $handler): void
    {
        if (!isset($this->handlers[$eventClassName])) {
            $this->handlers[$eventClassName] = [];
        }

        $this->handlers[$eventClassName][] = $handler;
    }

    public function dispatch(DomainEventInterface $event): void
    {
        $eventClass = $event::class;

        if (!isset($this->handlers[$eventClass])) {
            $this->logger->info("No handlers registered for {$eventClass}");

            return;
        }

        foreach ($this->handlers[$eventClass] as $handler) {
            try {
                $handler($event);
            } catch (Exception $e) {
                $this->logger->error("Error handling event {$eventClass}: " . $e->getMessage());
            }
        }
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            if (!$event instanceof DomainEventInterface) {
                throw new InvalidArgumentException('Expected DomainEvent instance');
            }
            $this->dispatch($event);
        }
    }
}
