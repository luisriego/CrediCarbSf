<?php

declare(strict_types=1);

namespace Domain\Common;

use function array_column;

enum ProjectStatus: string
{
    case PLANNED = 'Planned';
    case IN_DEVELOPMENT = 'Em Desenvolvimento';
    case APPROVED = 'Aprovado';
    case IN_EXECUTION = 'Em Execução';
    case COMPLETED = 'Concluído';
    case CANCELED = 'Cancelado';
    case SUSPENDED = 'Suspenso';

    public function getCompletionPercentage(): int
    {
        return match ($this) {
            self::PLANNED => 0,
            self::IN_DEVELOPMENT => 25,
            self::APPROVED => 50,
            self::IN_EXECUTION => 75,
            self::COMPLETED => 100,
            self::CANCELED => 0,
            self::SUSPENDED => 0
        };
    }

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
