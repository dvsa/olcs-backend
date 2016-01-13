<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence - Review Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ReviewDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'reviewDate';
    const QUERY_CLASS = Qry::class;
}
