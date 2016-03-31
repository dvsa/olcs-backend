<?php
/**
 * BkmServiceDurOrDate
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\IrfoPsvAuthBundle as Qry;

/**
 * BkmServiceDurOrDate
 */
class BkmServiceDurOrDate extends SingleValueAbstract
{
    const FORMATTER = 'Date';
    const FIELD  = 'expiryDate';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'irfoPsvAuth';
    const QUERY_CLASS = Qry::class;
}
