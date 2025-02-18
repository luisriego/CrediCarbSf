<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\DeleteCertificationAuthority\Dto;

use App\Domain\Model\CertificationAuthority;

readonly class DeleteCertificationAuthorityByIdOutputDto
{
    public function __construct(public array $data) {}

    public static function create(CertificationAuthority $certificationAuthority): self
    {
        return new self(
            [],
        );
    }
}
