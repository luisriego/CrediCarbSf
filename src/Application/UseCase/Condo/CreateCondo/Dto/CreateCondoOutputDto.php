<?php

declare(strict_types=1);

namespace App\Application\UseCase\Condo\CreateCondo\Dto;

class CreateCondoOutputDto
{
    public string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
