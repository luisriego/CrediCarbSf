<?php 

declare(strict_types=1);

namespace App\Application\UseCase\Project\CreateProjectService\Dto;

final class CreateProjectOutputDto
{
    public function __construct(public readonly string $projectId) {}

    public function getProjectId(): string
    {
        return $this->projectId;
    }
}