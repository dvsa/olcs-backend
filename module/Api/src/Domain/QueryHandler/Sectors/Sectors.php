<?php

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Sectors;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class Sectors extends AbstractListQueryHandler
{
    protected $repoServiceName = 'Sectors';

    public function handleQuery(QueryInterface $query)
    {

        $repo = $this->getRepo();
        $results = [];

        foreach ($repo->fetchList($query) as $row) {
            $row['allocatedPermits'] = $repo->calculatePermitsNumber($row['siftingPercentage']);
            $row['applicationsTotal'] = $repo->getApplicationsTotal($row['sectorId']);
            $results[] = $row;
        }

        return [
          'result' => $results,
          'count' => $repo->fetchCount($query)
        ];

    }
}
















/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Sectors;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Sectors as SectorsEntity;
use Dvsa\Olcs\Api\Domain\Repository\Sectors as Repo;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractListQueryHandler;
*/

/**
 * Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
/*class Sectors extends AbstractQueryHandler
{
    protected $repoServiceName = 'Sectors';

    public function handleQuery(QueryInterface $query)
    {

        $sectorList = Repo::fetchAllSectors($query, Query::HYDRATE_OBJECT);
        //$tt = $this->resultList($sectorList);
        //echo '<pre>'; var_dump($sectorList);die();

        //$repo = $this->getRepo();
        //$repo->fetchAllSectors();
        //$sectorList = array(1,2,3);
        return [
          'result' => $this->resultList($sectorList),
          'count' => count($sectorList),
        ];
    }
}*/

