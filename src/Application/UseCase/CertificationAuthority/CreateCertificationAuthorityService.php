<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority;

use App\Application\UseCase\CertificationAuthority\Dto\CreateCertificationAuthorityInputDto;
use App\Application\UseCase\CertificationAuthority\Dto\CreateCertificationAuthorityOutputDto;
use App\Domain\Exception\CertificationAuthority\CertificationAuthorityAlreadyExistsException;
use App\Domain\Model\CertificationAuthority;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;

readonly class CreateCertificationAuthorityService
{
    public function __construct(
        private CertificationAuthorityRepositoryInterface $repository,
    ) {}

    public function handle(CreateCertificationAuthorityInputDto $inputDto): CreateCertificationAuthorityOutputDto
    {
        $certificationAuthority = CertificationAuthority::create(
            $inputDto->taxpayer,
            $inputDto->name,
            $inputDto->website,
        );

        if ($this->repository->exists($certificationAuthority)) {
            throw CertificationAuthorityAlreadyExistsException::repeatedCertificationAuthority();
        }

        $this->repository->add($certificationAuthority, true);

        return new CreateCertificationAuthorityOutputDto($certificationAuthority->getId());
    }
}
