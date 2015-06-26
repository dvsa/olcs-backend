<?php

/**
 * Licence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Licence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Overview extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Application'];

    public function handleQuery(QueryInterface $query)
    {
        $licence = $this->getRepo()->fetchUsingId($query);

        $discCriteria = Criteria::create();
        $discCriteria
            ->where($discCriteria->expr()->isNull('ceasedDate'));

        $vehicleCriteria = Criteria::create();
        $vehicleCriteria
            ->where($vehicleCriteria->expr()->isNull('removalDate'))
            ->andWhere($vehicleCriteria->expr()->neq('specifiedDate', null));

        $statusCriteria = Criteria::create();
        $statusCriteria
            ->where($statusCriteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                ]
            ));

        $applications = $this->getOtherApplicationsFromLicence($licence);
        $openCases = $this->getOpenCases($licence);

        return $this->result(
            $licence,
            [
                'licenceType',
                'status',
                'goodsOrPsv',
                'organisation' => [
                    'tradingNames',
                    'licences' => [
                        'criteria' => $statusCriteria,
                        'status',
                    ],
                    'leadTcArea',
                ],
                'psvDiscs' => [
                    'criteria' => $discCriteria,
                ],
                'licenceVehicles' => [
                    'criteria' => $vehicleCriteria,
                ],
                'operatingCentres',
                'changeOfEntitys',
                'trafficArea',
                'gracePeriods',
            ],
            [
                'currentApplications' => $this->resultList($applications),
                'openCases' => $this->resultList($openCases, ['publicInquirys']),
                'tradingName' => $licence->getTradingName(),
                'complaintsCount' => $licence->getOpenComplaintsCount(),
            ]
        );
    }

    protected function getOtherApplicationsFromLicence($licence)
    {
        $organisationId = $licence->getOrganisation()->getId();
        return $this->getRepo('Application')->fetchActiveForOrganisation($organisationId);
    }

    protected function getOpenCases($licence)
    {
        $allCases = (array) $licence->getCases()->getIterator();
        return array_filter(
            $allCases,
            function ($case) {
                return $case->isOpen();
            }
        );

    }
}
