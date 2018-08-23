<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Get a list of Sectors
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class SectorsList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
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
