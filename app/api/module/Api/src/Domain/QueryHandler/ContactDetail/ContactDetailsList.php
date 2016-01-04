<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContactDetail;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of countries
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ContactDetailsList extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContactDetails';

    public function handleQuery(QueryInterface $query)
    {
        /* @var $repo \Dvsa\Olcs\Api\Domain\Repository\ContactDetails */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList($repo->fetchList($query, Query::HYDRATE_OBJECT)),
            'count' => $repo->fetchCount($query)
        ];
    }
}
