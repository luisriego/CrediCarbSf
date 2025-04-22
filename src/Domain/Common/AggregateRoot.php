<?php

declare(strict_types=1);

namespace App\Domain\Common;

use App\Domain\Bus\Event\DomainEvent;

abstract class AggregateRoot
{
    private array $domainEvents = [];

    final public function pullDomainEvents(): array
    {
        $recordedDomainEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $recordedDomainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }
}
