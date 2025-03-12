<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\GetCertificationAuthorityById\Dto;

use App\Domain\Model\CertificationAuthority;

readonly class GetCertificationAuthorityByIdIOutputDto
{
    public function __construct(public array $data) {}

    public static function create(CertificationAuthority $certificationAuthority): self
    {
        return new self(
            [
                'id' => $certificationAuthority->getId(),
                'taxpayer' => $certificationAuthority->taxPayer(),
                'name' => $certificationAuthority->getName(),
                'website' => $certificationAuthority->getWebsite(),
                'createdOn' => $certificationAuthority->getCreatedOn(),
                'updatedOn' => $certificationAuthority->getUpdatedOn(),
            ],
        );
    }
}
