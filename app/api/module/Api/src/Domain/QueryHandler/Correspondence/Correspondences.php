<?php

/**
 * Correspondences.php
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
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

    public function handleQuery(QueryInterface $query)
    {
        // Object hydration to enforce JsonSerialize.
        $result = $this->getRepo()
            ->fetchList($query, Query::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList($result, ['licence', 'document']),
            'count' => $this->getRepo()->fetchCount($query),
            'feeCount' => $this->getFeeCount($query->getOrganisation()),
        ];
    }

    /**
     * @param int $organisationId
     * @return int
     */
    protected function getFeeCount($organisationId)
    {
        return $this->getRepo('Fee')->getOutstandingFeeCountByOrganisationId($organisationId, true);
    }
}
