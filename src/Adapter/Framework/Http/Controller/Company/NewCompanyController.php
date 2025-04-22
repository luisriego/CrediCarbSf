<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\CreateCompanyRequestDto;
use App\Application\Command\Company\CreateCompanyCommand;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

readonly class NewCompanyController
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    #[Route('/api/v1/companies/{id}', methods: ['PUT'])]
    public function __invoke(string $id, CreateCompanyRequestDto $requestDto): JsonResponse
    {
        $command = new CreateCompanyCommand(
            CompanyId::fromString($id)->value(),
            CompanyName::fromString($requestDto->fantasyName)->value(),
            CompanyTaxpayer::fromString($requestDto->taxpayer)->value(),
        );

        $this->commandBus->dispatch($command);

        return new JsonResponse(
            [
                'message' => 'Company created successfully',
            ],
            Response::HTTP_CREATED,
        );
    }
}
