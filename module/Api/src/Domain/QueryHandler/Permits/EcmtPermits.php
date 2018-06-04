<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get ECMT Permit Applications
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class EcmtPermits extends AbstractQueryHandler
{
    protected $repoServiceName = 'EcmtPermits';

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        
    }
}
