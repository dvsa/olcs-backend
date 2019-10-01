<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * IrhpPermitNumber
 */
class IrhpPermitNumber extends SingleValueAbstract
{
    const FIELD  = 'permitNumber';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
}
