<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\CreateCertificationAuthority\Dto;

final readonly class CreateCertificationAuthorityOutputDto
{
    public function __construct(public string $certificationAuthorityId) {}

    public function getCertificationAuthorityId(): string
    {
        return $this->certificationAuthorityId;
    }
}
