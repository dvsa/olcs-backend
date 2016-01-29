<?php

/**
 * FStanding Additional Veh Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * FStanding Additional Veh Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FStandingAdditionalVeh extends AbstractQueryHandler
{
    protected $repoServiceName = 'FinancialStandingRate';

    public function handleQuery(QueryInterface $query)
    {
        $results = $this->getRepo()
            ->fetchLatestRateForBookmark($query->getGoodsOrPsv(), $query->getLicenceType(), $query->getEffectiveFrom());

        return ['Results' => $this->resultList($results)];
    }
}
