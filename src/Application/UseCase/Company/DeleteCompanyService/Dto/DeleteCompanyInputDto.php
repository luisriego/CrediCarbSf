<?php

declare(strict_types=1);

namespace App\Application\UseCase\Company\DeleteCompanyService\Dto;

use App\Domain\Exception\InvalidArgumentException;

use function is_null;

class DeleteCompanyInputDto
{
    private function __construct(
        public readonly string $id,
    ) {}

    public static function create(?string $id): self
    {
        if (is_null($id)) {
            throw InvalidArgumentException::createFromMessage('Company ID can\'t be null');
        }

        return new static($id);
    }
}
