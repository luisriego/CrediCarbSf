<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ChangePasswordService\Dto;

use App\Domain\Model\User;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;

use function is_null;

class ChangePasswordInputDto
{
    use AssertLengthRangeTrait;
    use AssertNotNullTrait;

    private const ARGS = ['id', 'oldPassword', 'newPassword'];

    public function __construct(
        public readonly ?string $id,
        public readonly ?string $oldPassword,
        public readonly ?string $newPassword,
    ) {
        $this->assertNotNull(self::ARGS, [$this->id, $this->oldPassword, $this->newPassword]);

        if (!is_null($this->newPassword)) {
            $this->assertValueRangeLength(
                $this->newPassword,
                User::MIN_PASSWORD_LENGTH,
                User::MAX_PASSWORD_LENGTH);
        }
    }

    public static function create(?string $id, ?string $oldPassword, ?string $newPassword): self
    {
        return new static($id, $oldPassword, $newPassword);
    }
}
