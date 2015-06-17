<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence Trailer Limit Bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceTrailerLimit extends SingleValueAbstract
{
    const FORMATTER = null;
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'totAuthTrailers';
    const QUERY_CLASS = Qry::class;
}
