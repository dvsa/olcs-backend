<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * InsNoVhls bookmark - number of weeks between vehicles inspections
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsNoVhls extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'safetyInsVehicles';
    const QUERY_CLASS = Qry::class;
}
