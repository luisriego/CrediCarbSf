<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\DeleteCondo;

use App\Application\UseCase\Condo\DeleteCondo\Dto\DeleteCondoInputDto;
use App\Domain\Repository\CondoRepositoryInterface;

class DeleteCondo
{
    public function __construct(
        private readonly CondoRepositoryInterface $condoRepository
    ) {
    }

    public function handle(DeleteCondoInputDto $dto): void
    {
        $condo = $this->condoRepository->findOneByIdOrFail($dto->id);

        $this->condoRepository->remove($condo, true);
    }
}
