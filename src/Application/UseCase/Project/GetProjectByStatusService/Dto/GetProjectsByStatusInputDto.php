<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectByStatusService\Dto;

class GetProjectsByStatusInputDto
{
    private const ARGS = ['status'];

    public function __construct(
        public readonly ?string $status,
    ) {}

    public static function create(?string $status): self
    {
        return new static($status);
    }
}
