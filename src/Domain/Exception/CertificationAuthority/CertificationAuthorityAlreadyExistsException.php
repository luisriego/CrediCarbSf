<?php

declare(strict_types=1);

namespace App\Domain\Exception\CertificationAuthority;

use Symfony\Component\HttpKernel\Exception\HttpException;

use function sprintf;

class CertificationAuthorityAlreadyExistsException extends HttpException
{
    public static function createFromName(string $name): self
    {
        return new self(400, sprintf('Certification Authority with name <%s> already exists', $name));
    }

    public static function repeatedCertificationAuthority(): self
    {
        return new self(400, 'Certification Authority already exists');
    }
}
