<?php

/**
 * ECMT Permits
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\EcmtPermits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;


/**
 * ECMT Permits
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class EcmtPermits extends AbstractListQueryHandler
{
    protected $repoServiceName = 'EcmtPermits';

        public function handleQuery(QueryInterface $query){
            $repo = $this->getRepo();
            $results = $repo->fetchData($query);
            return $results;
        }

}














