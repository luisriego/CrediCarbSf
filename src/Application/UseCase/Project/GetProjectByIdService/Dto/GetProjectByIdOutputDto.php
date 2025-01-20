<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectByIdService\Dto;

use App\Domain\Model\Project;

class GetProjectByIdOutputDto
{
    private function __construct(public array $data) {}

    public static function create(Project $project): self
    {
        return new self(
            [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'areaHa' => $project->getAreaHa(),
                'quantity' => $project->getQuantity(),
                'price' => $project->getPrice(),
                'projectType' => $project->getProjectType(),
                'status' => $project->getStatus(),
                'startDate' => $project->getStartDate(),
                'endDate' => $project->getEndDate(),
                'isActive' => $project->isActive(),
            ],
        );
    }
}
