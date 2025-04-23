<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\CreateCompanyRequestDto;
use App\Application\Command\Company\CreateCompanyCommand;
use App\Domain\ValueObject\CompanyId;
use App\Domain\ValueObject\CompanyName;
use App\Domain\ValueObject\CompanyTaxpayer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class NewCompanyController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    #[Route('/api/v1/companies', name:'create_company_command', methods: ['PUT'])]
    public function __invoke(CreateCompanyRequestDto $requestDto): JsonResponse
    {
        $command = new CreateCompanyCommand(
            CompanyId::fromString($requestDto->id)->value(),
            CompanyTaxpayer::fromString($requestDto->taxpayer)->value(),
            CompanyName::fromString($requestDto->fantasyName)->value(),
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
