<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle as Qry;

/**
 * Licence - In Force Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class IssueDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'licence';
    const FIELD = 'inForceDate';
    const QUERY_CLASS = Qry::class;
}
