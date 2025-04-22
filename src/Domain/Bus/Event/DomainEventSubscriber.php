<?php

declare(strict_types=1);

namespace App\Domain\Bus\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}
