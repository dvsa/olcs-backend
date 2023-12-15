<?php

/**
 * IrfoGvStartDate
 */

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrfoGvPermitBundle as Qry;

/**
 * IrfoGvStartDate
 */
class IrfoGvStartDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'inForceDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irfoGvPermit';
    const QUERY_CLASS = Qry::class;
}
