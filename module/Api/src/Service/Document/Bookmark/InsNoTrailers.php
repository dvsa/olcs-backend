<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * InsNoTrailers bookmark - number of weeks between trailer inspections
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsNoTrailers extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'safetyInsTrailers';
    const QUERY_CLASS = Qry::class;
}
