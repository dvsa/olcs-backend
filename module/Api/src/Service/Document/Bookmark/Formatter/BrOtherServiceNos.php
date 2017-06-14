<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter;

/**
 * BrOtherServiceNos formatter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrOtherServiceNos implements FormatterInterface
{
    const FORMAT = '(%s)';
    const SEPARATOR = ', ';

    /**
     * format bus reg service numbers
     *
     * @param array $serviceNos service numbers
     *
     * @return string
     */
    public static function format(array $serviceNos)
    {
        if (!empty($serviceNos)) {
            return sprintf(
                self::FORMAT,
                implode(
                    self::SEPARATOR,
                    array_column($serviceNos, 'serviceNo')
                )
            );
        }

        return '';
    }
}
