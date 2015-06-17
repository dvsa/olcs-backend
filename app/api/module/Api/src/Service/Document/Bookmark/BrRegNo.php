<?php
/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * Class
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrRegNo extends BusRegFlatAbstract
{
    const FORMATTER = null;
    const FIELD  = 'regNo';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
    const QUERY_CLASS = Qry::class;
}
