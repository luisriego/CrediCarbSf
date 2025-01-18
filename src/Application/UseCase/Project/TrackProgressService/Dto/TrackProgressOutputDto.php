<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\TrackProgressService\Dto;

class TrackProgressOutputDto
{
    public function __construct(
        public readonly string $currentStatus,
        public readonly array $milestones,
        public readonly ?string $startDate,
        public readonly ?string $endDate,
        public readonly int $completionPercentage,
    ) {}
}
