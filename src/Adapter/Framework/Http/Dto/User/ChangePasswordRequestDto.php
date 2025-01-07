<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

readonly class ChangePasswordRequestDto implements RequestDto
{
    public ?string $oldPassword;
    public ?string $newPassword;

    public function __construct(Request $request)
    {
        $this->oldPassword = $request->request->get('oldPassword');
        $this->newPassword = $request->request->get('newPassword');
    }
}
