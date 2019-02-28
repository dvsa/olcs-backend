<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter;

/**
 * Date2 formatter
 *
 * Example: 01 January 1970
 */
class DateDayMonthYear implements FormatterInterface
{
    public static function format(array $data)
    {
        return date("d F Y", strtotime(reset($data)));
    }
}
