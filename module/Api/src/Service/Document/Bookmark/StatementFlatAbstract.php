<?php

/**
 * Statement Flat Abstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Formatter;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\StatementBundle as Qry;

/**
 * Statement Flat Abstract
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
abstract class StatementFlatAbstract extends SingleValueAbstract
{
    const CLASS_NAMESPACE = __NAMESPACE__;
    const FORMATTER = null;
    const FIELD = null;
    const SRCH_VAL_KEY = 'statement';
    const QUERY_CLASS = Qry::class;
}
