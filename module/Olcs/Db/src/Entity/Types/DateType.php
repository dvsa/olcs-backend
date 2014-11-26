<?php

/**
 * Custom date type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Entity\Types;

use Doctrine\DBAL\Types\DateType as DoctrineDateType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Custom date type
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DateType extends DoctrineDateType
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
            return $value->format('Y-m-d');
        }

        $val = \DateTime::createFromFormat('!'.$platform->getDateFormatString(), $value);
        if (!$val) {
            throw ConversionException::conversionFailedFormat(
                $value,
                $this->getName(),
                $platform->getDateFormatString()
            );
        }

        if ($val instanceof \DateTime) {
            return $val->format('Y-m-d');
        }

        return $val;
    }
}
