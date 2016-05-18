<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\OrganisationBundle as Qry;

/**
 * IrfoOpName
 */
class IrfoOpName extends SingleValueAbstract
{
    const FIELD  = 'name';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'organisation';
    const QUERY_CLASS = Qry::class;
}
