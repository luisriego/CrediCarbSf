<?php

declare(strict_types=1);

namespace App\Adapter\Database\ORM\Doctrine\Type;

use App\Domain\ValueObject\Taxpayer;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TaxpayerType extends Type
{
    public const NAME = 'taxpayer_type';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value instanceof Taxpayer ? $value->value() : null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value !== null ? Taxpayer::fromString($value) : null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
