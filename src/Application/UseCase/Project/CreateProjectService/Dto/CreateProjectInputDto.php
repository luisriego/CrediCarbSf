<?php

declare(strict_types=1);

namespace App\Application\UseCase\Project\CreateProjectService\Dto;

use App\Domain\Validation\Traits\AssertNotNullTrait;

class CreateProjectInputDto
{
    use AssertNotNullTrait;

    private const ARGS = [
        'name',
        'description',
        'areaHa',
        'quantity',
        'price',
        'projectType',
    ];

    public function __construct(
        public string $name,
        public string $description,
        public string $areaHa,
        public string $quantity,
        public string $price,
        public string $projectType,
        public ?string $owner,
    ) {
        $this->assertNotNull(self::ARGS, [
            $this->name,
            $this->description,
            $this->areaHa,
            $this->quantity,
            $this->price,
            $this->projectType,
        ]);
    }

    public static function create(
        ?string $name,
        ?string $description,
        ?string $areaHa,
        ?string $quantity,
        ?string $price,
        ?string $projectType,
        ?string $owner,
    ): self {
        return new static($name, $description, $areaHa, $quantity, $price, $projectType, $owner);
    }
}
