<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\CreateProjectService;

use App\Application\UseCase\Project\CreateProjectService\Dto\CreateProjectInputDto;
use App\Application\UseCase\Project\CreateProjectService\Dto\CreateProjectOutputDto;
use App\Domain\Exception\Project\ProjectAlreadyExistsException;
use App\Domain\Model\Project;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\ProjectRepositoryInterface;

readonly class CreateProjectService
{
    public function __construct(
        private ProjectRepositoryInterface $projectRepository,
        private CompanyRepositoryInterface $companyRepository,
    ) {}

    public function handle(CreateProjectInputDto $inputDto): CreateProjectOutputDto
    {
        // replace whit an TDA method like existByIdOrFail()
//        if ($this->companyRepository->existById($inputDto->owner) === false) {
//            throw ProjectAlreadyExistException::ownerNotFound();
//        }

        $companyOwner = $this->companyRepository->findOneByIdOrFail($inputDto->owner);

        $project = Project::create(
            $inputDto->name,
            $inputDto->description,
            $inputDto->areaHa,
            $inputDto->quantityInKg,
            $inputDto->priceInCents,
            $inputDto->projectType,
            $companyOwner,
        );

        if ($this->projectRepository->exists($project)) {
            throw ProjectAlreadyExistsException::repeatedProject();
        }

        if ($this->projectRepository->existsWithSimilarWords($project)) {
            throw ProjectAlreadyExistsException::repeatedProject();
        }

        $this->projectRepository->add($project, true);

        return new CreateProjectOutputDto($project->getId());
    }
}
