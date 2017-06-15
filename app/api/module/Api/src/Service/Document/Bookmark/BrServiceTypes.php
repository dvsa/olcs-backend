<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class BrServiceTypes
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrServiceTypes extends SingleValueAbstract
{
    const FORMATTER = 'BrServiceTypes';
    const FIELD  = 'busServiceTypes';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = DynamicBookmark::PARAM_BUSREG_ID;
    const BUNDLE = ['busServiceTypes'];
    const QUERY_CLASS = Qry::class;
}
