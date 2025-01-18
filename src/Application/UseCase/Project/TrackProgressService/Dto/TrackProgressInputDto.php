<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\TrackProgressService\Dto;

use App\Domain\Validation\Traits\AssertValidUidTrait;

class TrackProgressInputDto
{
    use AssertValidUidTrait;

    public function __construct(
        public readonly string $projectId,
    ) {
        $this->assertValidUid($this->projectId);
    }

    public static function create(string $projectId): self
    {
        return new self($projectId);
    }
}
