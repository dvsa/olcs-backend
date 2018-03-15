<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\CompaniesHouse\AlertList as AlertListQuery;
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
     * @param QueryInterface|AlertListQuery $query Query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository\CompaniesHouseAlert $repo */
        $repo = $this->getRepo();

        $results = [];

        $companiesHouseAlerts = $repo->fetchCaListWithLicences($query);

        $results = $this->resultList(
            $repo->fetchList($query, \Doctrine\ORM\Query::HYDRATE_OBJECT),
            [
                'organisation',
                'reasons' => [
                    'reasonType',
                ],
            ]
        );
        $licences = [];
        foreach ($companiesHouseAlerts as $companiesHouseAlert) {
            $companiesHouseAlert->getOrganisation()->getActiveLicences();
            $licences [] = $this->resultList(
                [$companiesHouseAlert],
                ['organisation' => ['licences'], 'reasons' => ['reasonType']]
            );
            foreach ($licences[0] as $licence) {
                $data [] = $licence;
            }

        }


        /**
         * foreach ($companiesHouseAlerts as $companiesHouseAlert) {
         * $result = $this->resultList([$companiesHouseAlert],
         * ['organisation' => ['licences'], 'reasons' => ['reasonType']]);
         * foreach ($companiesHouseAlert[0]->getOrganisation()->getLicences() as $licence) {
         * $result['licence'] = $this->resultList([$licence])[0];
         * }
         * $results[] = $result;
         * }
         **/
        return [
            'result' => $results,
            'count' => $repo->fetchCount($query),
            'valueOptions' => ['companiesHouseAlertReason' => $repo->getReasonValueOptions(),]
        ];
    }
}
