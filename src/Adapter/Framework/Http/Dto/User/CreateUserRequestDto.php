<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use App\Domain\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class CreateUserRequestDto implements RequestDto
{
    public string $name;
    public string $email;
    public string $password;
    public int $age;

    public function __construct(Request $request)
    {
        if (empty($request->request->get('name'))
            || empty($request->request->get('email'))
            || empty($request->request->get('password'))
            || empty($request->request->get('age'))
        ) {
            throw InvalidArgumentException::createFromMessage('All fields are required');
        }

        $this->name = $request->request->get('name');
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
        $this->age = $request->request->get('age');
    }
}
