<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitStockBundle as Qry;

/**
 * IrhpExpiryDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpExpiryDate extends SingleValueAbstract
{
    const FORMATTER = 'DateDayMonthYear';
    const FIELD  = 'validTo';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermitStock';
    const QUERY_CLASS = Qry::class;
}
