<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpApplicationBundle as Qry;

/**
 * IrhpApplicationReference
 */
class IrhpApplicationReference extends SingleValueAbstract
{
    const FIELD = 'applicationRef';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpAppId';
    const QUERY_CLASS = Qry::class;
}
