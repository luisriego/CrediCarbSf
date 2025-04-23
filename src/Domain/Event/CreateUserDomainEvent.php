<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Bus\Event\DomainEvent;

final class CreateUserDomainEvent extends DomainEvent
{
    public function __construct(
        string $aggregateId,
        private readonly string $name,
        private readonly string $email,
        private readonly string $password,
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
            $body['name'],
            $body['email'],
            $body['password'],
            $eventId,
            $occurredOn,
        );
    }

    public static function eventName(): string
    {
        return 'user.create';
    }

    public function toPrimitives(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }
}
