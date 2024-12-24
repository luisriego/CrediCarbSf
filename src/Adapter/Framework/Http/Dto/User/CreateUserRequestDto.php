<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class CreateUserRequestDto implements RequestDto
{
    public string $name;
    public string $email;
    public string $password;
    public int $age;

    public function __construct(Request $request)
    {
        $this->name = $request->request->get('name');
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
        $this->age = $request->request->get('age');
    }
}
