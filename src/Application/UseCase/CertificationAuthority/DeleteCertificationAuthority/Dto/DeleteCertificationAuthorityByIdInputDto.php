<?php

declare(strict_types=1);

namespace App\Application\UseCase\CertificationAuthority\DeleteCertificationAuthority\Dto;

use App\Domain\Validation\Traits\AssertValidUidTrait;

class DeleteCertificationAuthorityByIdInputDto
{
    use AssertValidUidTrait;

    public function __construct(public ?string $id)
    {
        $this->assertValidUid($id);
    }

    public static function create(?string $id): self
    {
        return new static($id);
    }
}
