<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface EventSourcedEntityInterface
{
    /**
     * @return DomainEventInterface[]
     */
    public function releaseEvents(): array;
}
