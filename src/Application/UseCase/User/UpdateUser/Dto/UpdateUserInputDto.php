<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\UpdateUser\Dto;

use App\Domain\Model\User;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertMinimumAgeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

use function is_null;

class UpdateUserInputDto
{
    use AssertLengthRangeTrait;
    use AssertMinimumAgeTrait;
    use AssertNotNullTrait;

    private const ARGS = ['id'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $name,
        public readonly ?int $age,
        public readonly ?string $company,
        public readonly array $paramsToUpdate,
    ) {
        $this->assertNotNull(self::ARGS, [$this->id]);

        if (!is_null($this->name)) {
            $this->assertValueRangeLength($this->name, User::NAME_MIN_LENGTH, User::NAME_MAX_LENGTH);
        }

        if (!is_null($this->age)) {
            $this->assertMinimumAge($this->age, User::MIN_AGE);
        }
    }

    public static function create(?string $id, ?string $name, ?int $age, ?string $company, array $paramsToUpdate): self
    {
        return new static($id, $name, $age, $company, $paramsToUpdate);
    }
}
