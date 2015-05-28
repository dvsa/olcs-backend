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

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}
