<?php

/**
 * Custom date time type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Entity\Types;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType as DoctrineDateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Custom date time type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateTimeType extends DoctrineDateTimeType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof \DateTime) {
            return $value->format(\DateTime::ISO8601);
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);

        if (!$val) {
            $val = date_create($value);
        }

        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateTimeFormatString()
            );
        }

        if ($val instanceof \DateTime) {
            return $val->format(\DateTime::ISO8601);
        }

        return $val;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value !== null && !($value instanceof \DateTime)) {
            $value = new DateTime($value);
        }

        return ($value !== null)
            ? $value->format($platform->getDateTimeFormatString()) : null;
    }
}
