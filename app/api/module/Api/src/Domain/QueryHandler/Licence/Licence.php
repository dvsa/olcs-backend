<?php

/**
 * Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Licence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['ContinuationDetail', 'Note'];

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    /**
     * Factory
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        $licence = $this->getRepo()->fetchUsingId($query);

        $this->auditRead($licence);

        $continuationDetail = $this->getContinuationDetail($licence);
        $continuationDetailResponse = ($continuationDetail) ?
            $this->result($continuationDetail, ['continuation', 'licence'])->serialize() :
            null;
        $latestNote = $this->getRepo('Note')->fetchForOverview($query->getId());

        return $this->result(
            $licence,
            [
                'isExpired',
                'isExpiring',
                'cases' => [
                    'appeal' => [
                        'outcome',
                        'reason',
                    ],
                    'stays' => [
                        'stayType',
                        'outcome',
                    ],
                ],
                'correspondenceCd' => [
                    'address',
                ],
                'status',
                'goodsOrPsv',
                'licenceType',
                'trafficArea',
                'organisation' => [
                    'organisationPersons' => [
                        'person' => ['disqualifications']
                    ],
                    'tradingNames',
                    'type',
                    'disqualifications',
                ],
                'licenceStatusRules' => ['licenceStatus'],
            ],
            [
                'sections' => $this->sectionAccessService->getAccessibleSectionsForLicence($licence),
                'niFlag' => $licence->getNiFlag(),
                'isMlh' => $licence->getOrganisation()->isMlh(),
                'continuationMarker' => $continuationDetailResponse,
                'latestNote' => $latestNote,
                'canHaveInspectionRequest' => !$licence->isSpecialRestricted(),
            ]
        );
    }

    /**
     * Get a Continuation Detail for the marker
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence Licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail|null
     */
    private function getContinuationDetail(\Dvsa\Olcs\Api\Entity\Licence\Licence $licence)
    {
        $continuationDetails = $this->getRepo('ContinuationDetail')->fetchForLicence($licence->getId());
        if (count($continuationDetails) > 0) {
            return $continuationDetails[0];
        }

        return null;
    }
}
