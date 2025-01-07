<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\API\Filter;

use App\Domain\Model\Company;
use InvalidArgumentException;

use function mb_strlen;
use function sprintf;

class CompanyFilter extends EntityFilter
{
    public readonly ?string $taxpayer;
    public readonly ?string $fantasyName;

    public function __construct(
        int $page,
        int $limit,
        string $sort,
        string $order,
        ?string $name,
        ?string $taxpayer,
        ?string $fantasyName,
    ) {
        parent::__construct($page, $limit, $sort, $order, $name);
        $this->taxpayer = $taxpayer;
        $this->fantasyName = $fantasyName;
    }

    public function validateTaxpayer(): void
    {
        if ($this->taxpayer !== null && (mb_strlen($this->taxpayer) < Company::TAXPAYER_MIN_LENGTH || mb_strlen($this->taxpayer) > Company::TAXPAYER_MAX_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid taxpayer length [%s]', $this->taxpayer));
        }
    }

    public function validateFantasyName(): void
    {
        if ($this->fantasyName !== null && (mb_strlen($this->fantasyName) < Company::NAME_MIN_LENGTH || mb_strlen($this->fantasyName) > Company::NAME_MAX_LENGTH)) {
            throw new InvalidArgumentException(sprintf('Invalid fantasy name length [%s]', $this->fantasyName));
        }
    }
}
