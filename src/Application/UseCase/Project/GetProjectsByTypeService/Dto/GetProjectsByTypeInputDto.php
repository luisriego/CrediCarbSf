<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByTypeService\Dto;

class GetProjectsByTypeInputDto
{
    private const ARGS = ['type'];

    public function __construct(
        public readonly ?string $type,
    ) {}

    public static function create(?string $type): self
    {
        return new static($type);
    }
}
