<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Publication;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * List of pending publications
 */
final class PendingList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Publication';

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchPendingList(),
                [
                    'pubStatus',
                    'trafficArea',
                    'document'
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
