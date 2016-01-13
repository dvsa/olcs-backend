<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Task;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Task extends AbstractQueryHandler
{
    protected $repoServiceName = 'Task';

    public function handleQuery(QueryInterface $query)
    {
        $task = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $task,
            [
                'category',
                'subCategory',
                'assignedToTeam',
                'assignedToUser',
                'assignedByUser' => [
                    'contactDetails' => [
                        'person'
                    ]
                ]
            ]
        );
    }
}
