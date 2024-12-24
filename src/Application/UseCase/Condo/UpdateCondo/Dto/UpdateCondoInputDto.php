<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\UpdateCondo\Dto;

use App\Domain\Model\Condo;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertMinimumAgeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class UpdateCondoInputDto
{
    use AssertLengthRangeTrait;
    use AssertMinimumAgeTrait;
    use AssertNotNullTrait;

    private const ARGS = ['id'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $taxpayer,
        public readonly ?string $fantasyName,
        public readonly array $paramsToUpdate
    ) {
        $this->assertNotNull(self::ARGS, [$this->id]);

        if (!\is_null($this->taxpayer)) {
            $this->assertValueRangeLength($this->taxpayer, Condo::TAXPAYER_MIN_LENGTH, Condo::TAXPAYER_MAX_LENGTH);
        }

        if (!\is_null($this->fantasyName)) {
            $this->assertValueRangeLength($this->fantasyName, Condo::NAME_MIN_LENGTH, Condo::NAME_MAX_LENGTH);
        }
    }

    public static function create(?string $id, ?string $taxpayer, ?string $fantasyName, array $paramsToUpdate): self
    {
        return new static($id, $taxpayer, $fantasyName, $paramsToUpdate);
    }
}
