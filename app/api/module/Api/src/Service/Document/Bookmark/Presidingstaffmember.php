<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\HearingBundle as Qry;

/**
 * Presidingstaffmember
 */
class Presidingstaffmember extends SingleValueAbstract
{
    const FIELD  = 'presidingStaffName';
    const SRCH_FLD_KEY = 'case';
    const SRCH_VAL_KEY = 'case';
    const QUERY_CLASS = Qry::class;
}
