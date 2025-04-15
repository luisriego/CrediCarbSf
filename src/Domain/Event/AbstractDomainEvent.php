<?php

declare(strict_types=1);

namespace App\Domain\Event;

use DateTimeImmutable;

abstract class AbstractDomainEvent implements DomainEventInterface
{
    private DateTimeImmutable $occurredOn;

    public function __construct()
    {
        $this->occurredOn = new DateTimeImmutable();
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
