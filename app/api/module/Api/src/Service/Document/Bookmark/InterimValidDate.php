<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle as Qry;

/**
 * Interim Licence - Valid Date
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class InterimValidDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const SRCH_VAL_KEY = 'application';
    const FIELD = 'interimStart';
    const QUERY_CLASS = Qry::class;
}
