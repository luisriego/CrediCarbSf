<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\UpdateCertificationAuthority\Dto;

use App\Domain\Model\CertificationAuthority;

readonly class UpdateCertificationAuthorityOutputDto
{
    public function __construct(public CertificationAuthority $certificationAuthority) {}

    public static function create(CertificationAuthority $certificationAuthority): self
    {
        return new static($certificationAuthority);
    }
}
