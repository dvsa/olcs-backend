<?php

/**
 * BkmNoAuths
 */

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrfoPsvAuthBundle as Qry;

/**
 * BkmNoAuths
 */
class BkmNoAuths extends SingleValueAbstract
{
    const FIELD  = 'copiesRequiredTotal';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irfoPsvAuth';
    const QUERY_CLASS = Qry::class;
}
