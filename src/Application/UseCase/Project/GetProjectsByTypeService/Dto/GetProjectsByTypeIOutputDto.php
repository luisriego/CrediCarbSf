<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByTypeService\Dto;

use function array_map;

class GetProjectsByTypeIOutputDto
{
    public function __construct(public array $data) {}

    public static function create(array $projects): self
    {
        return new self(
            array_map(
                fn ($project) => [
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
                $projects,
            ),
        );
    }
}
