<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\GetAllCertificationsAuthority;

use App\Domain\Model\CertificationAuthority;
use App\Domain\Repository\CertificationAuthorityRepositoryInterface;

use function array_map;

readonly class GetAllCertificationsAuthorityService
{
    public function __construct(
        private CertificationAuthorityRepositoryInterface $authorityRepository,
    ) {}

    public function handle(): array
    {
        /** @var CertificationAuthority[] $certificationAuthorities */
        $certificationAuthorities = $this->authorityRepository->findAll();

        return array_map(function ($certificationAuthority) {
            return [
                $certificationAuthority->toArray(),
                //                'id' => $certificationAuthority->getId(),
                //                'taxpayer' => $certificationAuthority->taxPayer(),
                //                'name' => $certificationAuthority->getName()
            ];
        }, $certificationAuthorities);
    }
}
