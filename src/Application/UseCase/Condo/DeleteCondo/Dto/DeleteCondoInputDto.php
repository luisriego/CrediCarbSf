<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\DeleteCondo\Dto;

use App\Domain\Exception\InvalidArgumentException;

class DeleteCondoInputDto
{
    private function __construct(
        public readonly string $id
    ) {
    }

    public static function create(?string $id): self
    {
        if (\is_null($id)) {
            throw InvalidArgumentException::createFromMessage('Condo ID can\'t be null');
        }

        return new static($id);
    }
}
