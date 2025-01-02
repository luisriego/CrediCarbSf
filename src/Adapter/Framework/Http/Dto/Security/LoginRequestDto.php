<?php

namespace App\Adapter\Framework\Http\Dto\Security;

use App\Adapter\Framework\Http\Dto\RequestDto;
use App\Domain\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

readonly class LoginRequestDto implements RequestDto
{
    public string $email;
    public string $password;
    public function __construct(Request $request)
    {
        if (empty($request->request->get('email')) ||
            empty($request->request->get('password'))
        ) {
            throw InvalidArgumentException::createFromMessage('All fields are required');
        }
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
    }
}