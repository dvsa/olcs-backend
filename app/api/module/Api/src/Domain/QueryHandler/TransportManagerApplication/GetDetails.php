<?php

/**
 * Get a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerApplication';

    protected $extraRepos = [
        'ApplicationOperatingCentre',
        'LicenceOperatingCentre',
        'PreviousConviction',
        'OtherLicence',
        'TmEmployment'
    ];

    public function handleQuery(QueryInterface $query)
    {
        /* @var $tma TransportManagerApplication */
        $tma = $this->getRepo()->fetchDetails($query->getId());

        // populate the required associated entities
        $this->getRepo()->fetchWithOperatingCentres($query->getId());
        $this->getRepo('ApplicationOperatingCentre')->fetchByApplication($tma->getApplication()->getId());
        $this->getRepo('LicenceOperatingCentre')->fetchByLicence($tma->getApplication()->getLicence()->getId());
        $this->getRepo('PreviousConviction')->fetchByTransportManager($tma->getTransportManager()->getId());
        $this->getRepo('OtherLicence')->fetchByTransportManager($tma->getTransportManager()->getId());
        $this->getRepo('TmEmployment')->fetchByTransportManager($tma->getTransportManager()->getId());

        return $this->result(
            $tma,
            [
                'application' => [
                    'licence' => [
                        'operatingCentres' => [
                            'operatingCentre' => [
                                'address' => [
                                    'countryCode'
                                ]
                            ]
                        ],
                        'organisation',
                    ],
                    'operatingCentres' => [
                        'operatingCentre' => [
                            'address' => [
                                'countryCode'
                            ]
                        ]
                    ]
                ],
                'operatingCentres' => [
                    'address' => [
                        'countryCode'
                    ]
                ],
                'transportManager' => [
                    'homeCd' => [
                        'address' => [
                            'countryCode'
                        ],
                        'person'
                    ],
                    'workCd' => [
                        'address' => [
                            'countryCode'
                        ],
                        'person'
                    ],
                    'otherLicences',
                    'previousConvictions',
                    'employments' => [
                        'contactDetails' => [
                            'address' => [
                                'countryCode'
                            ],
                            'person'
                        ]
                    ],
                    'documents' => [
                        'application',
                        'category',
                        'subCategory',
                    ],
                ],
                'otherLicences'
            ],
            [
                'isTmLoggedInUser' => $this->getCurrentUser()->getTransportManager() === $tma->getTransportManager()
            ]
        );
    }
}
