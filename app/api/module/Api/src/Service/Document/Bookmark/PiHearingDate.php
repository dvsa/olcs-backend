<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PiHearingBundle as Qry;

/**
 * PiHearingDate
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class PiHearingDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'hearingDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'hearing';
    const QUERY_CLASS = Qry::class;
}
