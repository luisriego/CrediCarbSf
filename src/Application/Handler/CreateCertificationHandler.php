<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Application\Command\CreateCertificationCommand;
use App\Domain\Model\Certification;
use App\Domain\Repository\CertificationRepositoryInterface;

class CreateCertificationHandler
{
    public function __construct(
        private CertificationRepositoryInterface $certificationRepository
    ) {}

    public function handle(CreateCertificationCommand $command): void
    {
        $certification = new Certification(
            $command->getName(),
            $command->getDescription(),
            $command->getType()
        );

        $this->certificationRepository->save($certification, true);
    }
}