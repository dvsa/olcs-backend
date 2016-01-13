<?php

/**
 * Task Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Task;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Task Details
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskSearchView';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query, Query::HYDRATE_ARRAY);
    }
}
