<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

/**
 * StatementRequestDate
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class StatementRequestDate extends StatementFlatAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'requestedDate';
    const SRCH_VAL_KEY = 'statement';
}
