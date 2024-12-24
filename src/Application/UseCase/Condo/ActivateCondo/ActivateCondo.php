<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\ActivateCondo;

use App\Application\UseCase\Condo\ActivateCondo\Dto\ActivateCondoInputDto;
use App\Application\UseCase\Condo\ActivateCondo\Dto\ActivateCondoOutputDto;
use App\Domain\Repository\CondoRepositoryInterface;

class ActivateCondo
{
    public function __construct(private readonly CondoRepositoryInterface $condoRepository)
    {
    }

    public function handle(ActivateCondoInputDto $inputDto): ActivateCondoOutputDto
    {
        $condo = $this->condoRepository->findOneByIdOrFail($inputDto->id);

        $condo->toggleActive();

        $condo->markAsUpdated();

        $this->condoRepository->save($condo, true);

        return ActivateCondoOutputDto::createFromModel($condo);
    }
}
