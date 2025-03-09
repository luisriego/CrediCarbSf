<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShoppingCart\ViewCartSummary\Dto;

readonly class ViewCartSummaryOutputDto
{
    public function __construct(
        public array $items,
        public string $total,
    ) {}
}
