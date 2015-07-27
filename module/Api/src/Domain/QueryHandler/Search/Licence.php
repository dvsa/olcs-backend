<?php

/**
 * Search by LicenceId Details
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Search;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Search by LicenceId Details
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class Licence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Organisation'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $organisation = $licence->getOrganisation();

        /** @var Organisation $organisation */
//        $organisation = $this->getRepo('Organisation')->fetchBusinessDetailsById($organisation->getId());

        return $this->result(
            $licence,
            [],
            [
                'trafficArea' => $this->result(
                    $licence->getTrafficArea()
                )->serialize(),
                'organisation' => $this->result(
                    $licence->getOrganisation(),
                    [
                        'type',
                        'contactDetails' => [
                            'address' => [
                                'countryCode'
                            ]
                        ],
                        'natureOfBusinesses',
                        'tradingNames',
                        'companySubsidiaries',
                        'leadTcArea',
                    ]
                )->serialize(),
                'correspondenceAddress' => $this->result(
                    $licence->getCorrespondenceCd(),
                    [
                        'person',
                        'phoneContacts',
                        'address' => [
                            'countryCode'
                        ]
                    ]
                )->serialize(),
                'partners' => $this->resultList(
                    $licence->getOrganisation()->getOrganisationPersons(),
                    [
                        'person'
                    ]
                ),
                'directors' => $this->resultList(
                    $licence->getOrganisation()->getOrganisationPersons(),
                    [
                        'person'
                    ]
                ),
                'transportManagers' => $this->resultList(
                    $licence->getTmLicences(),
                    [
                        'transportManager' => [
                            'homeCd' => [
                                'person'
                            ]
                        ]

                    ]
                ),
                'operatingCentres' => $this->resultList(
                    $licence->getOperatingCentres(),
                    [
                        'operatingCentre' => [
                            'address' => [
                                'countryCode'
                            ]
                        ]
                    ]
                ),
                'vehicles' => $this->resultList(
                    $licence->getLicenceVehicles(),
                    [
                        'vehicle'

                    ]
                ),
                'applications' => $this->resultList(
                    $licence->getApplications()

                ),
                'conditionUndertakings' => $this->resultList(
                    $licence->getConditionUndertakings()
                ),
                'otherLicences' => $this->resultList($licence->getOtherActiveLicences())
            ]
        );
    }
}
