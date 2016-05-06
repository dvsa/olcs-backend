<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Reason;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Reason as ReasonRepo;

/**
 * Reason List QueryHandler
 */
final class ReasonList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Reason';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ReasonRepo $repo */
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
