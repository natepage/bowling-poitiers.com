<?php
declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Dbal\Type;

use Carbon\CarbonImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

final class CarbonImmutableType extends DateTimeImmutableType
{
    /**
     * @param mixed $value
     *
     * @throws \Doctrine\DBAL\Types\ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?CarbonImmutable
    {
        $value = parent::convertToPHPValue($value, $platform);

        return $value !== null ? CarbonImmutable::instance($value) : null;
    }
}
