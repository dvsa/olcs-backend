<?php

/**
 * Get continuation details list
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get continuation details list
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GetList extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';

    protected $extraRepos = ['Continuation'];

    public function handleQuery(QueryInterface $query)
    {
        $results = $this->getRepo()->fetchDetails(
            $query->getContinuationId(),
            $query->getLicenceStatus(),
            $query->getLicenceNo(),
            $query->getMethod(),
            $query->getStatus()
        );
        $header = $this->getRepo('Continuation')->fetchWithTa($query->getContinuationId());
        return [
            'result' => $results,
            'count' => count($results),
            'header' => $header
        ];
    }
}
