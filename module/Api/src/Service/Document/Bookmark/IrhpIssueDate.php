<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpPermitBundle as Qry;

/**
 * IrhpIssueDate
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class IrhpIssueDate extends SingleValueAbstract
{
    const FORMATTER = 'DateDayMonthYear';
    const FIELD  = 'issueDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpPermit';
    const QUERY_CLASS = Qry::class;
}
