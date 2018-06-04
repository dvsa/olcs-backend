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
    //protected $repoServiceName = 'EcmtPermits';
    protected $repoServiceName = 'EcmtPermitCountryLink';
    protected $extraRepos = ['ConstrainedCountries'];

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        $ecmtPermits = $repo->fetchList($query, Query::HYDRATE_OBJECT);

        $data = array();
        foreach ($ecmtPermits as $permit)
        {
            // if $permit->getCountry()->getIsEcmtState()
            $data[] = array(
              'id'          => $permit->getEcmtPermit()->getId(),
              'restrictions' => $this->getRepo('ConstrainedCountries')->existsByCountryId($permit->getCountry()->getId()),
              'status'  => $permit->getEcmtPermit()->getApplicationStatus()->getStatusName()
            );
        }

        $count = $repo->fetchCount($query);

        return [
          'count' => $count,
          'result' => $data
        ];
        
    }
}
