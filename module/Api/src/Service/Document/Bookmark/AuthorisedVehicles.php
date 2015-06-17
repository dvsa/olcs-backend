<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence - Total vehicle authority
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class AuthorisedVehicles extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'totAuthVehicles';
    const QUERY_CLASS = Qry::class;
}
