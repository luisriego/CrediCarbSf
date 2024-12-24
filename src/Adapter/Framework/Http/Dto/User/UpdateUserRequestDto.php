<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

class UpdateUserRequestDto implements RequestDto
{
    public readonly ?string $id;
    public readonly ?string $name;
    public readonly ?string $email;
    public readonly ?string $password;
    public readonly ?int $age;
    public readonly array $keys;

    public function __construct(Request $request)
    {
        $this->id = $request->request->get('id');
        $this->name = $request->request->get('name');
        $this->email = $request->request->get('email');
        $this->password = $request->request->get('password');
        $this->age = $request->request->get('age');
        $this->keys = \array_keys($request->request->all());
    }
}
