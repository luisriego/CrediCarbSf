<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\DeleteUser\Dto;

use App\Domain\Exception\InvalidArgumentException;

class DeleteUserInputDto
{
    private function __construct(
        public readonly string $id
    ) {
    }

    public static function create(?string $id): self
    {
        if (\is_null($id)) {
            throw InvalidArgumentException::createFromMessage('User ID can\'t be null');
        }

        return new static($id);
    }
}
