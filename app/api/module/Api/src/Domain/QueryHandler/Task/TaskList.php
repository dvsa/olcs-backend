<?php

/**
 * Task List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Task;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Task List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskSearchView';

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
            'result' => $this->getRepo()->fetchList($query),
            'count' => $this->getRepo()->fetchCount($query),
            'count-unfiltered' => $this->getRepo()->fetchCount($unfilteredQuery)
        ];
    }
}
