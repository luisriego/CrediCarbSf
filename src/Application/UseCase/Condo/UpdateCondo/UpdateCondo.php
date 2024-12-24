<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\UpdateCondo;

use App\Application\UseCase\Condo\UpdateCondo\Dto\UpdateCondoInputDto;
use App\Application\UseCase\Condo\UpdateCondo\Dto\UpdateCondoOutputDto;
use App\Domain\Repository\CondoRepositoryInterface;

class UpdateCondo
{
    private const SETTER_PREFIX = 'set';

    public function __construct(private readonly CondoRepositoryInterface $condoRepository)
    {
    }

    public function handle(UpdateCondoInputDto $dto): UpdateCondoOutputDto
    {
        $condo = $this->condoRepository->findOneByIdOrFail($dto->id);

        foreach ($dto->paramsToUpdate as $param) {
            $condo->{\sprintf('%s%s', self::SETTER_PREFIX, \ucfirst($param))}($dto->{$param});
        }
        $condo->markAsUpdated();

        $this->condoRepository->save($condo, true);

        return UpdateCondoOutputDto::createFromModel($condo);
    }
}
