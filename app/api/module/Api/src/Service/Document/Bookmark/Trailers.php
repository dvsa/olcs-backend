<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence - Total trailers authority
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Trailers extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'totAuthTrailers';
    const QUERY_CLASS = Qry::class;
}
