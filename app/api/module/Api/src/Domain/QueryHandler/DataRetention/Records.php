<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\DataRetention;

use Dvsa\Olcs\Transfer\Query\DataRetention\Records as QueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\DataRetention;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Record List associated to Data Retention Rule
 */
class Records extends AbstractQueryHandler
{
    protected $repoServiceName = 'DataRetention';

    /**
     * Return list of data retention rules that are enabled and action is 'Review'
     *
     * @param QueryHandler|QueryInterface $query Query for data retention rule list
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var DataRetention $repo */
        $repo = $this->getRepo();
        $records = $repo->fetchAllWithEnabledRules($query);

        return [
            'result' => $this->resultList(
                $records['results']
            ),
            'count' => $records['count']
        ];
    }
}
