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
        public int $areaHa,
        public int $quantity,
        public int $price,
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
        ?int $areaHa,
        ?int $quantity,
        ?int $price,
        ?string $projectType,
        ?string $owner,
    ): self {
        return new static($name, $description, $areaHa, $quantity, $price, $projectType, $owner);
    }
}
