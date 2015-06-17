<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;

/**
 * Interim Licence - Trailers
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimTrailers extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'application';
    const FIELD = 'interimAuthTrailers';
    const DEFAULT_VALUE = 0;
    const QUERY_CLASS = Qry::class;
}
