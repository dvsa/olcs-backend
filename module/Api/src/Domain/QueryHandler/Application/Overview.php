<?php

/**
 * Application Overview
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationTracking as ApplicationTrackingEntity;
use Dvsa\Olcs\Transfer\Query\Licence\Overview as LicenceOverviewQry;

/**
 * Application Overview
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Overview extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee', 'Opposition'];

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);

        $licenceQuery = LicenceOverviewQry::create(['id' => $application->getLicence()->getId()]);
        $licence = $this->getQueryHandler()->handleQuery($licenceQuery);

        return $this->result(
            $application,
            [
                'applicationCompletion',
                'applicationTracking',
                'status',
                'interimStatus',
                'licenceType',
                'goodsOrPsv',
            ],
            [
                'licence' => $licence->serialize(),
                'feeCount' => $this->getFeeCount($application),
                'oppositionCount' => $this->getOppositionCount($application),
                'valueOptions' => [
                    'tracking' => ApplicationTrackingEntity::getValueOptions(),
                ],
            ]
        );
    }

    protected function getFeeCount($application)
    {
        $fees = $this->getRepo('Fee')->fetchOutstandingFeesByApplicationId($application->getId());
        return count($fees);
    }

    protected function getOppositionCount($application)
    {
        $oppositions = $this->getRepo('Opposition')->fetchByApplicationId($application->getId());
        return count($oppositions);
    }
}
