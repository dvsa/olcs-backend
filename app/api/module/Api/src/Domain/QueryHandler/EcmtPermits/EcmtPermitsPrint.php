<?php

/**
 * ECMT Permits
 *
 * @author Kollol Shamsuddin <kol.shamsuddin@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\EcmtPermits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;


class EcmtPermitsPrint extends AbstractListQueryHandler
{
    protected $repoServiceName = 'EcmtPermits';

    public function handleQuery(QueryInterface $query){
        $repo = $this->getRepo();
        $results = $repo->fetchForPrint($query);
        return $results;
    }

}














