<?php

/**
 * Alert
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\CompaniesHouseAlert as CompaniesHouseAlertRepo;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * Alert
 */
class AlertList extends AbstractQueryHandler
{
    protected $repoServiceName = 'CompaniesHouseAlert';

    public function handleQuery(QueryInterface $query)
    {
        /** @var CompaniesHouseAlertRepo $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT),
                [
                    'organisation',
                    'reasons' => [
                        'reasonType',
                    ],
                ]
            ),
            'count' => $repo->fetchCount($query),
            'valueOptions' => [
                'companiesHouseAlertReason' => $this->getRepo()->getReasonValueOptions(),
            ]
        ];
    }
}
