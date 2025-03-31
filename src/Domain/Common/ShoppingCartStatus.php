<?php

declare(strict_types=1);

namespace App\Domain\Common;

use function array_column;

enum ShoppingCartStatus: string
{
    case ACTIVE = 'active';
    case PROCESSING = 'processing';
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
}
