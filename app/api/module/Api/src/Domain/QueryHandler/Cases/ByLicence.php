<?php

/**
 * Cases by licence
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Cases by licence
 */
class ByLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    protected $extraRepos = ['Licence'];

    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        $licence = $this->getRepo('Licence')->fetchById($query->getLicence());

        return [
            'result' => $this->resultList($repo->fetchList($query, Query::HYDRATE_OBJECT)),
            'count' => $repo->fetchCount($query),
            'licence' => $licence,
            'organisation' => $licence->getOrganisation(),
        ];
    }
}
