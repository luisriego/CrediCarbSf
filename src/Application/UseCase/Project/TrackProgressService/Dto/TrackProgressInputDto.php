<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\TrackProgressService\Dto;

class TrackProgressInputDto
{
    public function __construct(
        public readonly string $projectId
    ) {}

    public static function create(string $projectId): self
    {
        return new self($projectId);
    }
}