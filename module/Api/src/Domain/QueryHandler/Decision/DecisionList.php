<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Decision;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Decision as DecisionRepo;

/**
 * Decision List QueryHandler
 */
final class DecisionList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Decision';

    public function handleQuery(QueryInterface $query)
    {
        /** @var DecisionRepo $repo */
        $repo = $this->getRepo();
        $results = $repo->fetchUnpaginatedList($query, Query::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList(
                $results
            ),
            'count' => count($results)
        ];
    }
}
