<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repository;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList;
use Doctrine\ORM\Query as DoctrineQuery;

class OpenBusReg extends AbstractQueryHandler
{
    protected $repoServiceName = 'BusRegSearchView';

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        $busRegs = $this->resultList($repo->fetchActiveByLicence($query->getId()));
        return [
            'results' => $busRegs,
            'count' => count($busRegs)
        ];
    }
}
