<?php

declare(strict_types=1);

namespace App\Application\UseCase\User\ChangePasswordService\Dto;

use App\Domain\Model\User;

readonly class ChangePasswordOutputDto
{
    private function __construct(public array $userData) {}

    public static function createFromModel(User $user): self
    {
        return new static(['success' => 'Password changed successfully']);
    }
}
