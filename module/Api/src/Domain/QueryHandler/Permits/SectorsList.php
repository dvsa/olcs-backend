<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class SectorsList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Sectors';

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        $count = $repo->fetchCount($query);
        return [
          'result' => $this->resultList(
              $repo->fetchList($query, Query::HYDRATE_OBJECT)
          ),
          'count' => $count,
        ];
    }
}
