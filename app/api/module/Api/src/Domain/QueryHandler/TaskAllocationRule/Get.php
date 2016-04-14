<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\TaskAllocationRule;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a single TaskAllocationRule
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Get extends AbstractQueryHandler
{
    protected $repoServiceName = 'TaskAllocationRule';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return $this->result(
            $repo->fetchUsingId($query),
            [
                'category',
                'team',
                'user' => ['contactDetails' => ['person']],
                'trafficArea',
                'taskAlphaSplits',
            ]
        );
    }
}
