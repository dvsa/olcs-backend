<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;

/**
 * Interim Licence - Vehicles
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimVehicles extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'application';
    const FIELD = 'interimAuthVehicles';
    const QUERY_CLASS = Qry::class;
}
