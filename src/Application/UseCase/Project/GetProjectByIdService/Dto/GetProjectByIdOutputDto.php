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
                'areaHa' => $project->areaHa(),
                'quantity' => $project->quantityInKg(),
                'price' => $project->priceInCents(),
                'projectType' => $project->getProjectType(),
                'status' => $project->getStatus(),
                'startDate' => $project->getStartDate(),
                'endDate' => $project->getEndDate(),
                'isActive' => $project->isActive(),
            ],
        );
    }
}
