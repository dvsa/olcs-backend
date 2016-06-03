<?php

/**
 * Get a list of Transport Manager Licences and Transport Manager Applications
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmResponsibilities;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a list of Transport Manager Licences and Transport Manager Applications
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmResponsibilitiesList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerLicence';

    protected $extraRepos = ['TransportManagerApplication'];

    public function handleQuery(QueryInterface $query)
    {
        $tmLicences = $this->getRepo()->fetchForTransportManager(
            $query->getTransportManager(),
            [
                Licence::LICENCE_STATUS_VALID,
                Licence::LICENCE_STATUS_SUSPENDED,
                Licence::LICENCE_STATUS_CURTAILED
            ]
        );

        $tmApplications = $this->getRepo('TransportManagerApplication')->fetchForTransportManager(
            $query->getTransportManager(),
            [
                Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
                Application::APPLICATION_STATUS_NOT_SUBMITTED,
                Application::APPLICATION_STATUS_GRANTED
            ],
            true
        );
        return [
            'result' => $this->resultList(
                $tmLicences,
                [
                    'tmType',
                    'licence' => [
                        'organisation',
                        'status'
                    ],
                    'transportManager',
                    'operatingCentres'
                ]
            ),
            'count' => count($tmLicences),
            'tmApplications' => $this->resultList(
                $tmApplications,
                [
                    'tmType',
                    'application' => [
                        'licence' => [
                            'organisation'
                        ],
                        'status'
                    ],
                    'transportManager',
                    'operatingCentres',
                    'tmApplicationStatus'
                ]
            ),
            'tmApplicationsCount' => count($tmApplications)
        ];
    }
}
