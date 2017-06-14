<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class BrOtherServiceNos
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrOtherServiceNos extends SingleValueAbstract
{
    const FORMATTER = 'BrOtherServiceNos';
    const FIELD  = 'otherServices';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = DynamicBookmark::PARAM_BUSREG_ID;
    const BUNDLE = 'otherServices';
    const QUERY_CLASS = Qry::class;
}
