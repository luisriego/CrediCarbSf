<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\Dto\Project;

use App\Adapter\Framework\Http\Dto\RequestDto;
use Symfony\Component\HttpFoundation\Request;

final class CreateProjectRequestDto implements RequestDto
{
    public string $name;
    public string $description;
    public ?string $areaHa;
    public string $quantity;
    public string $price;
    public string $projectType;
    public ?string $owner;

    public function __construct(Request $request)
    {
        $this->name = $request->get('name');
        $this->description = $request->get('description');
        $this->areaHa = $request->get('areaHa');
        $this->quantity = $request->get('quantity');
        $this->price = $request->get('price');
        $this->projectType = $request->get('projectType');
        $this->owner = $request->get('owner');
    }
}
