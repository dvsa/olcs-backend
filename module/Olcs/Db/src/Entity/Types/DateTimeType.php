<?php

/**
 * Custom date time type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Entity\Types;

use Doctrine\DBAL\Types\DateTimeType as DoctrineDateTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

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
            return $value->format('Y-m-d H:i:s');
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);

        if ( ! $val) {
            $val = date_create($value);
        }

        if ( ! $val) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
        }

        if ($val instanceof \DateTime) {
            return $val->format('Y-m-d H:i:s');
        }

        return $val;
    }
}
