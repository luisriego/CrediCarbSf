<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\CreateCondo;

use App\Application\UseCase\Condo\CreateCondo\Dto\CreateCondoInputDto;
use App\Application\UseCase\Condo\CreateCondo\Dto\CreateCondoOutputDto;
use App\Domain\Exception\Condo\CondoAlreadyExistsException;
use App\Domain\Model\Condo;
use App\Domain\Model\User;
use App\Domain\Repository\CondoRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class CreateCondo
{
    public function __construct(
        private readonly CondoRepositoryInterface $repository,
        private readonly UserRepositoryInterface $userRepo,
        private readonly AuthorizationCheckerInterface $checker,
        private readonly Security $security
    ) {
    }

    public function handle(CreateCondoInputDto $inputDto): CreateCondoOutputDto
    {
        if (null !== $this->repository->findOneByTaxpayer($inputDto->taxpayer)) {
            throw CondoAlreadyExistsException::createFromTaxpayer($inputDto->taxpayer);
        }

        $condo = Condo::create(
            $inputDto->taxpayer,
            $inputDto->fantasyName,
        );

        /** @var User $authenticatedUser */
        $authenticatedUser = $this->security->getUser();

        if (!$this->checker->isGranted('ROLE_SYNDIC') && $authenticatedUser) {
            $condo->addUser($authenticatedUser);
            $authenticatedUser->setRoles(['ROLE_SYNDIC']);
            $this->userRepo->save($authenticatedUser, false);
        }

        $this->repository->save($condo, true);

        return new CreateCondoOutputDto($condo->getId());
    }
}
