<?php

/**
 * BrRouteNo
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\BusRegBundle as Qry;

/**
 * BrRouteNo
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class BrRouteNo extends SingleValueAbstract
{
    const FORMATTER = null;
    const FIELD  = 'routeNo';
    const SRCH_FLD_KEY = 'id';
    const SRCH_VAL_KEY = 'busRegId';
    const QUERY_CLASS = Qry::Class;
}
