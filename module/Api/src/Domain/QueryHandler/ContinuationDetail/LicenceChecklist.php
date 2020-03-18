<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\ContinuationDetail;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Licence Checklist for continuation
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklist extends AbstractQueryHandler
{
    protected $repoServiceName = 'ContinuationDetail';

    protected $extraRepos = ['ConditionUndertaking'];

    const CONDITIONS_UNDERTAKINGS_SECTION = 'conditions_undertakings';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    protected $sectionAccessService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var ContinuationDetailEntity $continuationDetail */
        $continuationDetail = $this->getRepo()->fetchWithLicence($query->getId());
        $licence = $continuationDetail->getLicence();
        $notRemoved = Criteria::create();
        $notRemoved->andWhere($notRemoved->expr()->isNull('removalDate'));

        $sections = $this->sectionAccessService->getAccessibleSectionsForLicenceContinuation($licence);
        $sections = $this->alterSections(array_keys($sections), $licence);

        $conditionsUndertakings = $this->getRepo('ConditionUndertaking')
            ->fetchListForLicenceReadOnly($licence->getId());

        return $this->result(
            $continuationDetail,
            [
                'licence' => [
                    'licenceType',
                    'status',
                    'goodsOrPsv',
                    'trafficArea',
                    'organisation' => [
                        'type',
                        'organisationPersons' => [
                            'person' => [
                                'title'
                            ]
                        ],
                        'organisationUsers' => [
                            'user' => [
                                'contactDetails' => [
                                    'person'
                                ],
                                'roles'
                            ],
                        ]
                    ],
                    'tradingNames',
                    'licenceVehicles' => [
                        'vehicle',
                        'criteria' => $notRemoved
                    ],
                    'correspondenceCd' => [
                        'address',
                        'phoneContacts' => [
                            'phoneContactType',
                        ],
                    ],
                    'establishmentCd' => [
                        'address',
                    ],
                    'operatingCentres' => [
                        'operatingCentre' => [
                            'address'
                        ]
                    ],
                    'tmLicences' => [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'title'
                                ]
                            ]
                        ]
                    ],
                    'workshops' => [
                        'contactDetails' => [
                            'person' => [
                                'title'
                            ],
                            'address'
                        ]
                    ],
                    'tachographIns'
                ]
            ],
            [
                'sections' => $sections,
                'ocChanges' => $licence->getOcPendingChanges(),
                'tmChanges' => $licence->getTmPendingChanges(),
                'hasConditionsUndertakings' => count($conditionsUndertakings) > 0
            ]
        );
    }

    /**
     * Alter sections
     *
     * @param array   $sections sections
     * @param Licence $licence licence
     *
     * @return array
     */
    protected function alterSections($sections, Licence $licence)
    {
        if (count($licence->getConditionUndertakings()) === 0
            && in_array(self::CONDITIONS_UNDERTAKINGS_SECTION, $sections)
        ) {
            unset($sections[array_search(self::CONDITIONS_UNDERTAKINGS_SECTION, $sections)]);
        }
        $filter = new UnderscoreToCamelCase();
        array_walk(
            $sections,
            function (&$item) use ($filter) {
                $item = lcfirst($filter->filter($item));
            }
        );
        return $sections;
    }
}
