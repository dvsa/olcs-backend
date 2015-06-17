<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

/**
 * S43AuthorisedDecision
 *
 * @author Shaun Lizzio <shaun.lizzio@valtech.co.uk>
 */
class S43AuthorisedDecision extends StatementFlatAbstract
{
    const FORMATTER = null;
    const FIELD  = 'authorisersDecision';
    const SRCH_VAL_KEY = 'statement';
}
