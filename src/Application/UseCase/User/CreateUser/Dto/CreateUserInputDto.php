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

    public string $id;
    public string $name;
    public string $email;
    public string $password;
    public int $age;

    private function __construct(string $id,string $name, string $email, string $password, int $age)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->age = $age;
    }

    public static function create(?string $id,?string $name, ?string $email, ?string $password, ?int $age): self
    {
        return new static($id, $name, $email, $password, $age);
    }
}
