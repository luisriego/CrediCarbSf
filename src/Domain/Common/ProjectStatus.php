<?php

declare(strict_types=1);

namespace Domain\Common;

enum ProjectStatus: string
{
    case PLANNED = 'Planned';
    case IN_DEVELOPMENT = 'Em Desenvolvimento';
    case APPROVED = 'Aprovado';
    case IN_EXECUTION = 'Em Execução';
    case COMPLETED = 'Concluído';
    case CANCELED = 'Cancelado';
    case SUSPENDED = 'Suspenso';

    public static function getValues(): array 
    {
        return array_column(self::cases(), 'value');
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
