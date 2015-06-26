<?php

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as ApplicationCompletionEntity;
use Dvsa\Olcs\Transfer\Query\Application\Application as ApplicationQry;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Review extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $defaultBundle = [
        'licence' => [
            'organisation' => []
        ]
    ];

    protected $sharedBundles = [
        'transport_managers' => [
            'transportManagers' => [
                'transportManager' => [
                    'homeCd' => [
                        'person' => [
                            'title'
                        ]
                    ]
                ]
            ]
        ],
        'operating_centres' => [
            'licence' => [
                'trafficArea'
            ],
            'operatingCentres' => [
                'application',
                'operatingCentre' => [
                    'address',
                    'adDocuments' => [
                        'application'
                    ]
                ]
            ]
        ],
        'vehicles' => [
            'licenceVehicles' => [
                'vehicle'
            ]
        ],
        'vehicles_psv' => [
            'licenceVehicles' => [
                'vehicle' => [
                    'psvType'
                ]
            ]
        ],
        'convictions_penalties' => [
            'previousConvictions' => [
                'title'
            ]
        ],
        'licence_history' => [
            'otherLicences' => [
                'previousLicenceType'
            ]
        ],
        'financial_history' => [
            'documents' => [
                'category',
                'subCategory'
            ]
        ],
        'conditions_undertakings' => [
            'conditionUndertakings' => [
                'conditionType',
                'attachedTo',
                'operatingCentre' => [
                    'address'
                ]
            ]
        ]
    ];

    protected $applicationBundles = [
        'business_type' => [
            'licence' => [
                'organisation' => [
                    'type'
                ]
            ]
        ],
        'business_details' => [
            'licence' => [
                'companySubsidiaries',
                'organisation' => [
                    'type',
                    'natureOfBusinesses',
                    'contactDetails' => [
                        'address'
                    ]
                ],
                'tradingNames'
            ]
        ],
        'safety' => [
            'licence' => [
                'workshops' => [
                    'contactDetails' => [
                        'address'
                    ]
                ],
                'tachographIns'
            ]
        ],
        'addresses' => [
            'licence' => [
                'correspondenceCd' => [
                    'address',
                    'phoneContacts' => [
                        'phoneContactType'
                    ]
                ],
                'establishmentCd' => [
                    'address'
                ]
            ]
        ],
        'taxi_phv' => [
            'licence' => [
                'trafficArea',
                'privateHireLicences' => [
                    'contactDetails' => [
                        'address'
                    ]
                ]
            ]
        ],
        'people' => [
            'licence' => [
                'organisation' => [
                    'type',
                    'organisationPersons' => [
                        'person' => [
                            'title'
                        ]
                    ]
                ]
            ],
            'applicationOrganisationPersons' => [
                'originalPerson',
                'person' => [
                    'title'
                ]
            ]
        ],
        'vehicles_declarations' => [
            'licence' => [
                'trafficArea'
            ]
        ]
    ];

    protected $variationBundles = [
        'type_of_licence' => [
            'licence' => [
                'licenceType'
            ]
        ],
        'people' => [
            'licence' => [
                'organisation' => [
                    'type'
                ]
            ],
            'applicationOrganisationPersons' => [
                'person' => [
                    'title'
                ]
            ]
        ],
        'conditions_undertakings' => [
            'conditionUndertakings' => [
                'licConditionVariation'
            ]
        ]
    ];

    protected $ignoredApplicationSections = [
        'community_licences'
    ];

    protected $ignoredVariationSections = [
        'community_licences'
    ];

    public function __construct()
    {
        $notRemovedCriteria = Criteria::create();
        $notRemovedCriteria->andWhere(
            $notRemovedCriteria->expr()->isNull('removalDate')
        );

        $this->sharedBundles['vehicles']['licenceVehicles']['criteria'] = $notRemovedCriteria;
        $this->sharedBundles['vehicles_psv']['licenceVehicles']['criteria'] = $notRemovedCriteria;
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $result = $this->getQueryHandler()->handleQuery(ApplicationQry::create($query->getArrayCopy()));
        $data = $result->serialize();

        $sections = array_keys($data['sections']);

        if ($application->isVariation()) {

            $sections = $this->filterVariationSections($sections, $application->getApplicationCompletion());

            $bundle = $this->getReviewDataBundleForVariation($sections);
        } else {
            $sections = $this->filterApplicationSections($sections);

            $bundle = $this->getReviewDataBundleForApplication($sections);
        }

        return $this->result(
            $application,
            $bundle,
            [
                'sections' => $sections,
                'isGoods' => $application->isGoods(),
                'isSpecialRestricted' => $application->isSpecialRestricted()
            ]
        );
    }

    protected function filterVariationSections($sections, ApplicationCompletionEntity $completion)
    {
        $sections = array_values(array_diff($sections, $this->ignoredVariationSections));

        $filter = new UnderscoreToCamelCase();

        foreach ($sections as $key => $section) {

            $getter = 'get' . ucfirst($filter->filter($section)) . 'Status';

            if ($completion->$getter() !== ApplicationEntity::VARIATION_STATUS_UPDATED) {
                unset($sections[$key]);
            }
        }

        return $sections;
    }

    protected function filterApplicationSections($sections)
    {
        return array_values(array_diff($sections, $this->ignoredApplicationSections));
    }

    protected function getReviewDataBundleForApplication(array $sections = [])
    {
        return $this->getReviewBundle($sections, 'application');
    }

    /**
     * Grab all of the review for a variation
     *
     * @param array $sections
     *
     * @return array
     */
    protected function getReviewDataBundleForVariation(array $sections = array())
    {
        return $this->getReviewBundle($sections, 'variation');
    }

    /**
     * Dynamically build the review bundle
     *
     * @param array $sections
     * @param string $lva
     * @return array
     */
    protected function getReviewBundle($sections, $lva)
    {
        $bundle = $this->defaultBundle;

        foreach ($sections as $section) {

            if (isset($this->sharedBundles[$section])) {
                $bundle = array_merge_recursive($bundle, $this->sharedBundles[$section]);
            }

            if (isset($this->{$lva . 'Bundles'}[$section])) {
                $bundle = array_merge_recursive($bundle, $this->{$lva . 'Bundles'}[$section]);
            }
        }

        return $bundle;
    }
}
