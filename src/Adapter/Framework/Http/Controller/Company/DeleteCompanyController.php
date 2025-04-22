<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Controller\Company;

use App\Application\UseCase\Company\DeleteCompanyService\DeleteCompanyService;
use App\Application\UseCase\Company\DeleteCompanyService\Dto\DeleteCompanyInputDto;
use App\Domain\Model\User;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class DeleteCompanyController
{
    public function __construct(
        public Security $security,
        public DeleteCompanyService $useCase,
    ) {}

    #[Route('/api/v1/companies/{id}', name: 'delete_company', methods: ['DELETE'])]
    public function invoke(string $id): Response
    {
        try {
            /** @var User $user */
            $user = $this->security->getToken()->getUser();

            $inputDto = DeleteCompanyInputDto::create($id, $user->getId());

            $this->useCase->handle($inputDto);

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (ForeignKeyConstraintViolationException $e) {
            return new JsonResponse(['error' => 'Cannot delete company with associated users'], Response::HTTP_CONFLICT);
        }
    }
}
