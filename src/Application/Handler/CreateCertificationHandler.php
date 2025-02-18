<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Common\CertificationType;
use App\Domain\Model\CertificationTypeEntity;
use App\Domain\Repository\CertificationTypeRepositoryInterface;

final class CreateCertificationHandler
{
    public function __construct(
        private readonly CertificationTypeRepositoryInterface $certificationRepository,
    ) {}

    public function handle(): void
    {
        foreach (CertificationType::cases() as $certificationType) {
            $certificationEntity = new CertificationTypeEntity(
                $certificationType->name,
                $certificationType->value,
            );

            $this->certificationRepository->save($certificationEntity, true);
        }
    }
}
