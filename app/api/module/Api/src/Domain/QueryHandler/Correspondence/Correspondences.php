<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Correspondences
 */
class Correspondences extends AbstractQueryHandler
{
    protected $repoServiceName = 'Correspondence';

    protected $extraRepos = ['Fee'];

    /**
     * Handle Query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Correspondence\Correspondences $query Query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository\Correspondence $repo */
        $repo = $this->getRepo();


        $data = [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'licence',
                    'document',
                ]
            ),
            'count' => $repo->fetchCount($query),
            'feeCount' => $this->getFeeCount($query->getOrganisation()),
        ];
        return $data;
    }

    /**
     * Get Count of Fees
     *
     * @param int $organisationId Org Id
     *
     * @return int
     */
    protected function getFeeCount($organisationId)
    {
        /** @var Repository\Fee $repo */
        $repo = $this->getRepo('Fee');

        return $repo->getOutstandingFeeCountByOrganisationId($organisationId, true, true);
    }
}
