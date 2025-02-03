<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById;

use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto\GetCertificationAuthorityByIdInputDto;
use App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto\GetCertificationAuthorityByIdIOutputDto;
use App\Domain\Model\CertificationAuthority;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;

readonly class GetCertificationAuthorityByIdService
{
    public function __construct(private CertificationAuthorityRepositoryInterface $repository) {}

    public function handle(GetCertificationAuthorityByIdInputDto $inputDto): GetCertificationAuthorityByIdIOutputDto
    {
        /** @var CertificationAuthority $certificationAuthority */
        $certificationAuthority = $this->repository->findOneByIdOrFail($inputDto->id);

        return GetCertificationAuthorityByIdIOutputDto::create($certificationAuthority);
    }
}
