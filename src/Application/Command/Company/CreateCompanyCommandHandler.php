<?php

declare(strict_types=1);

namespace App\Application\Command\Company;

use App\Application\UseCase\Company\CreateCompany\CreateCompany;
use App\Domain\Bus\Command\CommandHandler;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;

final readonly class CreateCompanyCommandHandler implements CommandHandler
{
    public function __construct(
        private CreateCompany $createCompany,
    ) {}

    public function __invoke(CreateCompanyCommand $command): void
    {
        $id = CompanyId::fromString($command->id());
        $taxpayer = CompanyTaxpayer::fromString($command->taxpayer());
        $fantasyName = CompanyName::fromString($command->fantasyName());

        $this->createCompany->create($id, $taxpayer, $fantasyName);
    }
}
