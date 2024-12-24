<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ActivateUser\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;

class ActivateUserInputDto
{
    use AssertNotNullTrait;

    private const ARGS = ['id', 'token'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $token,
    ) {
        $this->assertNotNull(self::ARGS, [$this->id, $this->token]);
    }

    public static function create(?string $id, ?string $token): self
    {
        return new static($id, $token);
    }
}
