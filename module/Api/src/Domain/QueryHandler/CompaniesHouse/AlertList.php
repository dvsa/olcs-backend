<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Alert
 */
class AlertList extends AbstractQueryHandler
{
    protected $repoServiceName = 'CompaniesHouseAlert';

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\CompaniesHouse\AlertList $query Query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository\CompaniesHouseAlert $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, \Doctrine\ORM\Query::HYDRATE_OBJECT),
                [
                    'organisation',
                    'reasons' => [
                        'reasonType',
                    ],
                ]
            ),
            'count' => $repo->fetchCount($query),
            'valueOptions' => [
                'companiesHouseAlertReason' => $repo->getReasonValueOptions(),
            ]
        ];
    }
}
