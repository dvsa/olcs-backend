<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class BrTasNotified
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrTasNotified extends SingleValueAbstract
{
    const FORMATTER = 'BrTasNotified';
    const FIELD  = 'trafficAreas';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = DynamicBookmark::PARAM_BUSREG_ID;
    const BUNDLE = ['trafficAreas'];
    const QUERY_CLASS = Qry::class;
}
