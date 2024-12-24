<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\CreateCondo\Dto;

use App\Domain\Model\Condo;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

class CreateCondoInputDto
{
    use AssertNotNullTrait;
    use AssertLengthRangeTrait;

    private const ARGS = [
        'taxpayer',
        'fantasyName',
    ];

    public function __construct(public string $taxpayer, public string $fantasyName)
    {
        $this->assertNotNull(self::ARGS, [$this->taxpayer, $this->fantasyName]);

        $this->assertValueRangeLength($this->taxpayer, Condo::TAXPAYER_MIN_LENGTH, Condo::TAXPAYER_MAX_LENGTH);
        $this->assertValueRangeLength($this->fantasyName, Condo::NAME_MIN_LENGTH, Condo::NAME_MAX_LENGTH);
    }

    public static function create(?string $taxpayer, ?string $fantasyName): self
    {
        return new static($taxpayer, $fantasyName);
    }

    private function taxpayerNumbers($value)
    {
        return preg_replace('/\D/', '', $value);
    }
}
