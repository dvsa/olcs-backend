<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Stay as StayRepo;
use Doctrine\ORM\Query;

/**
 * Stay QueryHandler
 */
final class StayList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Stay';

    public function handleQuery(QueryInterface $query)
    {
        /** @var StayRepo $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList($repo->fetchList($query, Query::HYDRATE_OBJECT), ['case']),
            'count' => $repo->fetchCount($query)
        ];
    }
}
