<?php

declare(strict_types=1);

namespace App\Domain\Exception\Project;

use App\Domain\Exception\HttpException;
use function sprintf;

final class ProjectAlreadyExistsException extends HttpException
{
    public static function createFromTaxPayer(string $taxPayer): self
    {
        return new self(400, sprintf('Project with tax payer <%s> already exists', $taxPayer));
    }

    public static function createFromFantasyName(string $email): self
    {
        return new self(400, sprintf('Project with Name <%s> already exists', $email));
    }

    public static function repeatedProject(): self
    {
        return new self(400, 'Project already exists');
    }

    public static function ownerNotFound(): self
    {
        return new self(404, 'Owner not found');
    }
}
