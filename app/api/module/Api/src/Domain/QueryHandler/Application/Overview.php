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
        /* @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);

        $licenceQuery = LicenceOverviewQry::create(['id' => $application->getLicence()->getId()]);
        $licence = $this->getQueryHandler()->handleQuery($licenceQuery, false);

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
                'outOfOppositionDate' => $this->getDateOrString($application->getOutOfOppositionDate()),
                'outOfRepresentationDate' => $this->getDateOrString($application->getOutOfRepresentationDate()),
                'operatingCentresNetDelta' => $application->getOperatingCentresNetDelta(),
                'licenceVehicles' => $this->resultList($application->getActiveVehicles()),
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

    /**
     * Conveert param into a string (ie if its a date)
     *
     * @param \DateTime|string $value
     *
     * @return string If param was a DateTime then returns a string version of the date, otherwise return param
     */
    protected function getDateOrString($value)
    {
        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        return $value;
    }
}
