<?php

/**
 * Bus
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Bus
 */
class Bus extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}
