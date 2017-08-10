<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\HearingBundle as Qry;

/**
 * Hearingtime
 */
class Hearingtime extends SingleValueAbstract
{
    const FORMATTER = 'Time';
    const FIELD  = 'hearingDate';
    const SRCH_FLD_KEY = 'case';
    const SRCH_VAL_KEY = 'case';
    const QUERY_CLASS = Qry::class;
}
