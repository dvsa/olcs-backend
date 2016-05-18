<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get List of Task Allocation Rules
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskAllocationRule';

    public function handleQuery(QueryInterface $query)
    {
        return [
            'result' => $this->resultList(
                $this->getRepo()->fetchList($query, \Doctrine\ORM\Query::HYDRATE_OBJECT),
                [
                    'category',
                    'team',
                    'user' => ['contactDetails' => ['person']],
                    'trafficArea',
                    'taskAlphaSplits' => ['user' => ['contactDetails' => ['person']]],
                ]
            ),
            'count' => $this->getRepo()->fetchCount($query),
            'count-unfiltered' => $this->getRepo()->hasRows($query),
        ];
    }
}
