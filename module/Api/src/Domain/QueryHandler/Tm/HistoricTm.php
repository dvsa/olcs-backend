<?php

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Historic TM
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class HistoricTm extends AbstractQueryHandler
{
    protected $repoServiceName = 'HistoricTm';

    /**
     * Handle HistoricTm query
     *
     * @param QueryInterface $query
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /* @var $repo Dvsa\Olcs\Api\Domain\Repository\HistoricTm */
        $repo = $this->getRepo();
        /* @var $transportManager \Dvsa\Olcs\Api\Entity\Tm\TransportManager */
        $transportManager = $repo->fetchUsingId($query);

        return $this->result(
            $transportManager
        );
    }
}
