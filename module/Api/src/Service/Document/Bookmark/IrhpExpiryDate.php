<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * IrhpEndDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpExpiryDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'expiryDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
}
