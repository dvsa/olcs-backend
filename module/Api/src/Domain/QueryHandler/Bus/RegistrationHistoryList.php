<?php

/**
 * Bus Registration History List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Bus as BusRepo;
use Dvsa\Olcs\Transfer\Query\Bus\RegistrationHistoryList as QueryCmd;
use Dvsa\Olcs\Api\Domain\Query\Bus\ByLicenceRoute as ByLicenceRouteQry;
use Doctrine\ORM\Query;

/**
 * Bus Registration History List
 */
class RegistrationHistoryList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    public function handleQuery(QueryInterface $query)
    {
        /**
         * @var BusRepo $repo
         * @var QueryCmd $query
         */
        $busReg = $this->getRepo()->fetchUsingId($query);

        $routeNoQuery = [
            'sort' => $query->getSort(),
            'order' => $query->getOrder(),
            'routeNo' => $busReg->getRouteNo(),
            'licenceId' => $busReg->getLicence()->getId(),
        ];

        $result = $this->getQueryHandler()->handleQuery(ByLicenceRouteQry::create($routeNoQuery), false);

        return $result;
    }
}
