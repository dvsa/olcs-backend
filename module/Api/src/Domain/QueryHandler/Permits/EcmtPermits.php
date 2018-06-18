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
    protected $extraRepos = ['Country'];

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        $ecmtPermits = $repo->fetchList($query, Query::HYDRATE_OBJECT);

        $data = array();
        foreach ($ecmtPermits as $permit)
        {
            $countries = $permit->getCountrys();
            $status = $permit->getStatus()->getId();
            $constrainedCountries = $this->getRepo('Country')->getConstrainedEcmtCountries(true)[1];

            $restrictions = false;
            foreach ($countries as $country)
            {
                if (in_array($country->getId(),$constrainedCountries))
                {
                    $restrictions = true;
                    break;
                }
            }

            $data[] = array(
              'id'          => $permit->getId(),
              'restrictions' => $restrictions,
              'status'  => $status
            );
        }

        return [
          'count' => count($data),
          'result' => $data
        ];
        
    }
}
