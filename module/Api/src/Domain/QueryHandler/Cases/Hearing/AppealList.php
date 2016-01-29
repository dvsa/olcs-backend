<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Hearing;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Appeal as AppealRepo;
use Doctrine\ORM\Query;

/**
 * Appeal QueryHandler
 */
final class AppealList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Appeal';

    public function handleQuery(QueryInterface $query)
    {
        /** @var AppealRepo $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList($repo->fetchList($query, Query::HYDRATE_OBJECT)),
            'count' => $repo->fetchCount($query)
        ];
    }
}
