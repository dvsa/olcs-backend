<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Processing;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\EventHistory;
use Doctrine\ORM\Query;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class History extends AbstractQueryHandler
{
    protected $repoServiceName = 'EventHistory';

    public function handleQuery(QueryInterface $query)
    {
        /** @var EventHistory $repo */
        $repo = $this->getRepo();
        $repo->disableSoftDeleteable();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'licence',
                    'application',
                    'organisation',
                    'transportManager',
                    'eventHistoryType',
                    'case',
                    'user' => [
                        'contactDetails' => [
                            'person'
                        ]
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
