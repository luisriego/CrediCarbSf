<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority;

use App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\Dto\UpdateCertificationAuthorityInputDto;
use App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\Dto\UpdateCertificationAuthorityOutputDto;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;

readonly class UpdateCertificationAuthorityService
{
    public function __construct(
        private CertificationAuthorityRepositoryInterface $repository,
    ) {}

    public function handle(UpdateCertificationAuthorityInputDto $inputDto): UpdateCertificationAuthorityOutputDto
    {
        $certificationAuthority = $this->repository->findOneByIdOrFail($inputDto->id);

        $certificationAuthority->setName($inputDto->name);
        $certificationAuthority->setWebsite($inputDto->website);

        $this->repository->save($certificationAuthority, true);

        return new UpdateCertificationAuthorityOutputDto($certificationAuthority);
    }
}
