<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectByIdService\Dto;

use App\Domain\Validation\Traits\AssertValidUidTrait;

class GetProjectByIdInputDto
{
    use AssertValidUidTrait;
    private const ARGS = ['id'];

    public function __construct(
        public readonly ?string $id,
    ) {
        $this->assertValidUid($this->id);
    }

    public static function create(?string $id): self
    {
        return new static($id);
    }
}
