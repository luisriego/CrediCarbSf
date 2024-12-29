<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\API\Filter;

use App\Domain\Model\Project;
use InvalidArgumentException;

use function mb_strlen;
use function sprintf;

class ProjectFilter extends EntityFilter
{
    public readonly ?string $areaHa;
    public readonly ?string $quantity;
    public readonly ?string $description;

    public function __construct(
        int $page,
        int $limit,
        string $sort,
        string $order,
        ?string $name,
        ?string $description,
        ?string $areaHa,
        ?string $quantity,
    ) {
        parent::__construct($page, $limit, $sort, $order, $name);
        $this->description = $description;
        $this->areaHa = $areaHa;
        $this->quantity = $quantity;
    }

    public function validateDescription(): void
    {
        if ($this->description !== null && (mb_strlen($this->description) < Project::DESCRIPTION_MIN_LENGTH || mb_strlen($this->description) > Project::DESCRIPTION_MAX_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid description length [%s]', $this->description));
        }
    }

    public function validateAreaHa(): void
    {
        if ($this->areaHa !== null && (mb_strlen($this->areaHa) < Project::AREA_MIN_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid areaHa length [%s]', $this->areaHa));
        }
    }

    public function validateQuantity(): void
    {
        if ($this->quantity !== null && (mb_strlen($this->quantity) < Project::QUANTITY_MIN_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid quantity length [%s]', $this->quantity));
        }
    }
}
