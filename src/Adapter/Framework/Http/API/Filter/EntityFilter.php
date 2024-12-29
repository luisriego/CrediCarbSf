<?php

declare(strict_types=1);

namespace App\Adapter\Framework\Http\API\Filter;

use InvalidArgumentException;

use function in_array;
use function sprintf;

class EntityFilter
{
    private const PAGE = 1;
    private const LIMIT = 10;
    private const ALLOWED_SORT_PARAMS = ['name'];
    private const ALLOWED_ORDER_PARAMS = ['asc', 'desc'];

    public readonly int $page;
    public readonly int $limit;

    public function __construct(
        int $page,
        int $limit,
        public readonly string $sort,
        public readonly string $order,
        public readonly ?string $name,
    ) {
        $this->page = $page !== 0 ? $page : self::PAGE;
        $this->limit = $limit !== 0 ? $limit : self::LIMIT;

        $this->validateSort($this->sort);
        $this->validateOrder($this->order);
    }

    private function validateSort(?string $sort): void
    {
        $sort = $sort ?: 'name';

        if (!in_array($sort, self::ALLOWED_SORT_PARAMS, true)) {
            throw new InvalidArgumentException(sprintf('Invalid sort param [%s]', $sort));
        }
    }

    private function validateOrder(?string $order): void
    {
        $order = $order ?: 'desc';

        if (!in_array($order, self::ALLOWED_ORDER_PARAMS, true)) {
            throw new InvalidArgumentException(sprintf('Invalid order param [%s]', $order));
        }
    }
}
