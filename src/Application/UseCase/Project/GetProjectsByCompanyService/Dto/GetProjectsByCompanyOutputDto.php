<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\GetProjectsByCompanyService\Dto;

use function array_map;

class GetProjectsByCompanyOutputDto
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
                    'areaHa' => $project->areaHa(),
                    'quantity' => $project->quantityInKg(),
                    'price' => $project->priceInCents(),
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
