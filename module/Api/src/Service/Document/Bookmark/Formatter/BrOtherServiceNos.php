<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter;

/**
 * BrOtherServiceNos formatter
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrOtherServiceNos extends AbstractArrayList
{
    const FORMAT = '(%s)';
    const SEPARATOR = ', ';
    const COLUMN = 'serviceNo';
}
