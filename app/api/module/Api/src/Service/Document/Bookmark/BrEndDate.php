<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class BrEndDate
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrEndDate extends SingleValueAbstract
{
    const DEFAULT_VALUE = 'N/A';
    const FORMATTER = 'Date';
    const FIELD  = 'endDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
    const QUERY_CLASS = Qry::class;
}
