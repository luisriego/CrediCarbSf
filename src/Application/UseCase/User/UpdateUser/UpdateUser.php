<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UpdateUser;

use App\Application\UseCase\User\UpdateUser\Dto\UpdateUserInputDto;
use App\Application\UseCase\User\UpdateUser\Dto\UpdateUserOutputDto;
use App\Domain\Exception\InvalidArgumentException;
use App\Domain\Repository\CompanyRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;

use function in_array;
use function sprintf;
use function ucfirst;

class UpdateUser
{
    private const SETTER_PREFIX = 'set';
    private const IMMUTABLE_FIELDS = ['id', 'email'];

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly CompanyRepositoryInterface $companyRepository,
    ) {}

    public function handle(UpdateUserInputDto $dto): UpdateUserOutputDto
    {
        $user = $this->userRepository->findOneByIdOrFail($dto->id);

        foreach ($dto->paramsToUpdate as $param) {
            if (in_array($param, self::IMMUTABLE_FIELDS, true)) {
                throw new InvalidArgumentException(sprintf('Cannot update %s because it is immutable', $param));
            }

            if ($param === 'password') {
                throw new InvalidArgumentException('Cannot update the password by this endpoint');
            }

            if ($param === 'company') {
                $company = $this->companyRepository->findOneByIdOrFail($dto->company);
                $user->setCompany($company);

                continue;
            }

            $user->{sprintf('%s%s', self::SETTER_PREFIX, ucfirst($param))}($dto->{$param});
        }

        $user->markAsUpdated();

        $this->userRepository->save($user, true);

        return UpdateUserOutputDto::createFromModel($user);
    }
}
