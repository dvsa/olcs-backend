<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class BrServiceNo
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrServiceNo extends BusRegFlatAbstract
{
    const DEFAULT_VALUE = 'N/A';
    const FIELD  = 'serviceNo';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
    const QUERY_CLASS = Qry::class;
}
