<?php

/**
 * Licence Overview
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as BusRegSearchViewRepository;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as SearchViewListQuery;

/**
 * Licence Overview
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class Overview extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Application', 'TrafficArea', 'BusRegSearchView'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        // Here we get the bus reg list - all we need is a count...

        // BusRegSearchView query object
        $busRegSearchViewParams = [
            'licId' => $licence->getId(),
            'page' => 1,
            'sort' => 'regNo',
            'order' => 'DESC',
            'limit' => 10
        ];
        $query = SearchViewListQuery::create($busRegSearchViewParams);
        /** @var BusRegSearchViewRepository $busRegSearchViewRepo */
        $busRegSearchViewRepo = $this->getRepo('BusRegSearchView');
        $busRegCount = $busRegSearchViewRepo->fetchCount($query);

        $discCriteria = Criteria::create();
        $discCriteria
            ->where($discCriteria->expr()->isNull('ceasedDate'));

        $vehicleCriteria = Criteria::create();
        $vehicleCriteria
            ->where($vehicleCriteria->expr()->isNull('removalDate'))
            ->andWhere($vehicleCriteria->expr()->neq('specifiedDate', null));

        $statusCriteria = Criteria::create();
        $statusCriteria->where(
            $statusCriteria->expr()->in(
                'status',
                [
                    LicenceEntity::LICENCE_STATUS_VALID,
                    LicenceEntity::LICENCE_STATUS_SUSPENDED,
                    LicenceEntity::LICENCE_STATUS_CURTAILED,
                ]
            )
        );

        $appCriteria = Criteria::create()->where(Criteria::expr()->eq('isVariation', false));

        $applications = $this->getOtherApplicationsFromLicence($licence);
        $trafficAreas = $this->getRepo('TrafficArea')->getValueOptions();

        return $this->result(
            $licence,
            [
                'busRegs',
                'licenceType',
                'status',
                'goodsOrPsv',
                'organisation' => [
                    'licences' => [
                        'criteria' => $statusCriteria,
                        'status',
                    ],
                    'leadTcArea',
                    'organisationUsers'
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
                'applications' => [
                    'criteria' => $appCriteria
                ]
            ],
            [
                'busCount' => $busRegCount,
                'currentApplications' => $this->resultList($applications),
                'openCases' => $this->resultList($licence->getOpenCases(), ['publicInquirys']),
                'tradingName' => $licence->getTradingName(),
                'complaintsCount' => $licence->getOpenComplaintsCount(),
                // extra data needed to populate select boxes
                'valueOptions' => [
                    'trafficAreas' => $trafficAreas,
                ]
            ]
        );
    }

    protected function getOtherApplicationsFromLicence($licence)
    {
        $organisationId = $licence->getOrganisation()->getId();
        return $this->getRepo('Application')->fetchActiveForOrganisation($organisationId);
    }
}
