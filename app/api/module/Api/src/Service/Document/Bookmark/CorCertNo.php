<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrhpApplicationBundle as Qry;

/**
 * CorCertNo
 */
class CorCertNo extends SingleValueAbstract
{
    const FIELD = 'corCertificateNumber';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irhpAppId';
    const QUERY_CLASS = Qry::class;
}
