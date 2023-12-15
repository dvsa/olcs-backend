<?php

/**
 * IrfoGvEndDate
 */

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrfoGvPermitBundle as Qry;

/**
 * IrfoGvEndDate
 */
class IrfoGvEndDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'expiryDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irfoGvPermit';
    const QUERY_CLASS = Qry::class;
}
