<?php

/**
 * BusReg History List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\BusRegHistory as Repository;
use Dvsa\Olcs\Transfer\Query\Bus\HistoryList as ListQueryObject;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * BusReg History List
 */
class HistoryList extends AbstractQueryHandler
{
    protected $repoServiceName = 'BusRegHistory';

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT), ['eventHistoryType', 'user']),
            'count' => $repo->fetchCount($query)
        ];
    }
}
