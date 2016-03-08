<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Si;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SeriousInfringement GetList QueryHandler
 */
final class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'SeriousInfringement';

    public function handleQuery(QueryInterface $query)
    {
        /* @var $repo \Dvsa\Olcs\Api\Domain\Repository\SeriousInfringement */
        $repo = $this->getRepo();
        $sis = $repo->fetchList($query, Query::HYDRATE_OBJECT);

        return [
            'results' => $this->resultList(
                $sis,
                ['siCategoryType']
            ),
            'count' => count($sis)
        ];

    }
}
