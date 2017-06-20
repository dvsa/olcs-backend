<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusFeeTypeBundle as Qry;

/**
 * Class BrFixed (bus reg fee amount)
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class BrFixed extends BusRegFlatAbstract
{
    const FIELD = 'fixedValue';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
    const QUERY_CLASS = Qry::class;
}
