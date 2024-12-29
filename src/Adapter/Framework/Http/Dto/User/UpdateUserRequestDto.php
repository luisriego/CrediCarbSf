<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\User;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

use function array_keys;

readonly class UpdateUserRequestDto implements RequestDto
{
    public ?string $id;
    public ?string $name;
    public ?int $age;
    public ?string $company;
    public array $keys;

    public function __construct(Request $request)
    {
        $this->id = $request->request->get('id');
        $this->name = $request->request->get('name');
        $this->age = $request->request->get('age');
        $this->company = $request->request->get('company');
        $this->keys = array_keys($request->request->all());
    }
}
