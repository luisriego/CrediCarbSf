<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\DeleteCertificationAuthority;

use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto\DeleteCertificationAuthorityByIdInputDto;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;

readonly class DeleteCertificationAuthorityService
{
    public function __construct(private CertificationAuthorityRepositoryInterface $repository) {}

    public function handle(DeleteCertificationAuthorityByIdInputDto $inputDto): void
    {
        $certificationAuthorityToDelete = $this->repository->findOneByIdOrFail($inputDto->id);

        $this->repository->remove($certificationAuthorityToDelete, true);
    }
}
