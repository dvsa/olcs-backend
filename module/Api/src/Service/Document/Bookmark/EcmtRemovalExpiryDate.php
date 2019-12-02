<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * EcmtRemovalExpiryDate
 */
class EcmtRemovalExpiryDate extends SingleValueAbstract
{
    const FORMATTER = 'DateDayMonthYear';
    const FIELD  = 'expiryDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
}
