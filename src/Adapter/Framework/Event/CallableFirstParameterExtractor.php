<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Event;

use App\Domain\Bus\Event\DomainEventSubscriber;

final class CallableFirstParameterExtractor
{
    public static function forPipedCallables(iterable $callables): array
    {
        return [];
    }

    public static function forCallables(iterable $callables): array
    {
        return [];
    }
}