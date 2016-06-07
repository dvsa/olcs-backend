<?php

/**
 * Task List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Task;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Task List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskSearchView';

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        $data = $query->getArrayCopy();

        unset($data['assignedToTeam']);
        unset($data['assignedToUser']);
        unset($data['category']);
        unset($data['taskSubCategory']);
        unset($data['date']);
        unset($data['status']);
        unset($data['urgent']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\Task\TaskList::create($data);

        return [
            'result' => $this->resultList($this->getRepo()->fetchList($query, Query::HYDRATE_OBJECT)),
            'count' => $this->getRepo()->fetchCount($query),
            'count-unfiltered' => $this->getRepo()->hasRows($unfilteredQuery),
        ];
    }
}
