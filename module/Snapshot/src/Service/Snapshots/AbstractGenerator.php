<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\View\Model\ViewModel;

/**
 * Abstract Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
abstract class AbstractGenerator implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $lva = 'application';

    protected $ignoredVariationSections = [
        'community_licences',
        'signature'
    ];
    protected $displayedAlwaysVariationSections = [
        'undertakings'
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
                'vehicle'
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
    protected $defaultBundle = [
        'licence' => [
            'organisation' => ['type']
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

    protected function generateReadonly(array $config, $template = 'review')
    {
        $model = new ViewModel($config);
        $model->setTerminal(true);
        $model->setTemplate('layout/' . $template);

        $renderer = $this->getServiceLocator()->get('ViewRenderer');
        return $renderer->render($model);
    }

    protected function filterVariationSections($sections, ApplicationCompletion $completion)
    {
        $sections = array_values(array_diff($sections, $this->ignoredVariationSections));

        $filter = new UnderscoreToCamelCase();

        foreach ($sections as $key => $section) {
            $getter = 'get' . ucfirst($filter->filter($section)) . 'Status';
            if (array_search($section, $this->displayedAlwaysVariationSections) === false &&
                $completion->$getter() !== Application::VARIATION_STATUS_UPDATED) {
                unset($sections[$key]);
            }
        }

        return $sections;
    }

    /**
     * getSections
     *
     * @param Application $application
     * @param             $sections
     *
     * @return array
     */
    protected function getSections(Application $application, $sections): array
    {
        if ($application->isVariation()) {
            $this->lva = 'variation';
            $sections = $this->filterVariationSections($sections, $application->getApplicationCompletion());

            $bundle = $this->getReviewDataBundleForVariation($sections);
        } else {
            $this->lva = 'application';
            $sections = $this->filterApplicationSections($sections);

            $bundle = $this->getReviewDataBundleForApplication($sections);
        }
        return array($sections, $bundle);
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
     * @param array  $sections
     * @param string $lva
     *
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
