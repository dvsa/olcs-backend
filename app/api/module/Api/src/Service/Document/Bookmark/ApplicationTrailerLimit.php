<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;

/**
 * Application Trailer Limit
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ApplicationTrailerLimit extends SingleValueAbstract
{
    const SRCH_VAL_KEY = 'application';
    const FIELD = 'totAuthTrailers';
    const DEFAULT_VALUE = 0;
    const QUERY_CLASS = Qry::class;
}
