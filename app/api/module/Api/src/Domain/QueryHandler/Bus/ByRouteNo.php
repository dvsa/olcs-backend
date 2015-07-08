<?php

/**
 * Bus Registration List (by routeNo)
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Bus Registration List (by routeNo)
 */
class ByRouteNo extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        return $this->resultList(
            $this->getRepo()->fetchList($query, Query::HYDRATE_OBJECT)
        );
    }
}
