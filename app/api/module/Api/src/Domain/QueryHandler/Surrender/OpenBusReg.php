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

    protected $extraRepos = ['Licence'];

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var SearchViewList $query */
        /** @var Repository $repo */
        $repo = $this->getRepo();

        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licence = $this->getRepo('Licence')->fetchById($query->getLicId());

        return [
            'result' => $this->resultList($repo->fetchActiveByLicence($licence)),
            'count' => $repo->fetchCount($query)
        ];
    }
}
