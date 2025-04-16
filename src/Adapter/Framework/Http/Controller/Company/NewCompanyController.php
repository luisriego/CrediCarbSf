<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Adapter\Framework\Http\Dto\Company\CreateCompanyRequestDto;
use App\Application\Command\Company\CreateCompanyCommand;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

readonly class NewCompanyController
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {}

    #[Route('/api/company/{id}', methods: ['PUT'])]
    public function __invoke(string $id, CreateCompanyRequestDto $requestDto): JsonResponse
    {
        $command = new CreateCompanyCommand(
            $id,
            $requestDto->fantasyName,
            $requestDto->taxpayer,
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
