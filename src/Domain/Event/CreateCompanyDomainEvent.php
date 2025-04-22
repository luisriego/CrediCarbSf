<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Bus\Event\DomainEvent;

final class CreateCompanyDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $taxpayer,
        private readonly string $fantasyName,
        string $eventId,
        string $occurredOn,
    ) {
        parent::__construct($aggregateId, $eventId, $occurredOn);
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn,
    ): self {
        return new self(
            $aggregateId,
            $body['taxpayer'],
            $body['fantasyName'],
            $eventId,
            $occurredOn,
        );
    }

    public static function eventName(): string
    {
        return 'company.create';
    }

    public function toPrimitives(): array
    {
        return [
            'taxpayer' => $this->taxpayer,
            'fantasyName' => $this->fantasyName,
        ];
    }

    public function taxpayer(): string
    {
        return $this->taxpayer;
    }

    public function fantasyName(): string
    {
        return $this->fantasyName;
    }
}
