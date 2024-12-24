<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\AddUserToCondo\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;

class AddUserToCondoInputDto
{
    use AssertNotNullTrait;

    private const ARGS = ['id', 'userId'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $userId
    ) {
        $this->assertNotNull(self::ARGS, [$this->id, $this->userId]);
    }

    public static function create(?string $id, ?string $userId): self
    {
        return new static($id, $userId);
    }
}
