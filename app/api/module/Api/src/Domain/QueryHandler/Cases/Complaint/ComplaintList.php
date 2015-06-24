<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Complaint;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Complaint as ComplaintRepo;

/**
 * Complaint QueryHandler
 */
final class ComplaintList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Complaint';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ComplaintRepo $repo */
        $repo = $this->getRepo();
        return [
            'result' => $this->resultList(
                    $repo->fetchList($query, Query::HYDRATE_OBJECT),
                    [
                        'complainantContactDetails' => [
                            'person'
                        ]
                    ]
                ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
