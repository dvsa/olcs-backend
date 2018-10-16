<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * IrhpStartDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpStartDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'issueDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
}
