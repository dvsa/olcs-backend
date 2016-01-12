<?php

/**
 * PiVenue
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\PiVenue;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\PiVenue as PiVenueRepo;

/**
 * PiVenue
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PiVenueList extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiVenue';

    public function handleQuery(QueryInterface $query)
    {
        /** @var PiVenueRepo $repo */
        $repo = $this->getRepo();
        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                ['address', 'trafficArea']
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}
