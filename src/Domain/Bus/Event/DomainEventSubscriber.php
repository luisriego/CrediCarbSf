<?php

namespace App\Domain\Bus\Event;

interface DomainEventSubscriber
{
    public static function subscribedTo(): array;
}