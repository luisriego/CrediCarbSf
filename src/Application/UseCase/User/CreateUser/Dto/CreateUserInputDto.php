<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\CreateUser\Dto;

use App\Domain\Model\User;
use App\Domain\Validation\Traits\AssertLengthRangeTrait;
use App\Domain\Validation\Traits\AssertMinimumAgeTrait;
use App\Domain\Validation\Traits\AssertNotNullTrait;
use App\Domain\Validation\Traits\AssertPasswordValidatorTrait;
use App\Domain\Validation\Traits\AssertValidEmailTrait;

class CreateUserInputDto
{
    use AssertNotNullTrait;
    use AssertMinimumAgeTrait;
    use AssertValidEmailTrait;
    use AssertLengthRangeTrait;
    use AssertPasswordValidatorTrait;

    private const ARGS = [
        'name',
        'email',
        'password',
        'age',
    ];
    private const MINIMUM_AGE = 18;

    public string $name;
    public string $email;
    public string $password;
    public int $age;

    private function __construct(string $name, string $email, string $password, int $age)
    {
        $this->age = $age;
        $this->password = $password;
        $this->email = $email;
        $this->name = $name;

        $this->assertNotNull(self::ARGS, [$this->name, $this->email, $this->password, $this->age]);

        $this->assertMinimumAge($this->age, User::MIN_AGE);

        $this->assertValueRangeLength($this->password, 6, 50);

        $this->assertValidEmail($this->email);

        $this->assertPassword($this->password);
    }

    public static function create(?string $name, ?string $email, ?string $password, ?int $age): self
    {
        return new static($name, $email, $password, $age);
    }
}
