<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Correspondences
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
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

        $iterableResult = $repo->fetchDocumentsList($query);

        $result = [];
        while (false !== ($row = $iterableResult->next())) {
            $row = current($row);

            $result[] = [
                'id' => $row['id'],
                'accessed' => $row['accessed'],
                'createdOn' => $row['createdOn'],

                'licence' => [
                    'id' => $row['licId'],
                    'licNo' => $row['licNo'],
                    'status' => $row['licStatus'],
                ],
                'document' => [
                    'description' => $row['docDesc'],
                ],
            ];
        }

        return [
            'result' => $result,
            'count' => count($result),
            'feeCount' => $this->getFeeCount($query->getOrganisation()),
        ];
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

        return $repo->getOutstandingFeeCountByOrganisationId($organisationId, true);
    }
}
