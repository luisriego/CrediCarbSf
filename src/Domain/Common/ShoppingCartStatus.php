<?php

declare(strict_types=1);

namespace App\Domain\Common;

enum ShoppingCartStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getValue(): string
    {
        return $this->value;
}
