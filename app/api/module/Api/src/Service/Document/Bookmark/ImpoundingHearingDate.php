<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ImpoundingBundle as Qry;

/**
 * Impounding Hearing Date
 */
class ImpoundingHearingDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'hearingDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'impounding';
    const QUERY_CLASS = Qry::class;
}
