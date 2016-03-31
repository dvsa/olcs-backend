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

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        $licence = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licence,
            [
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
                        'person'
                    ],
                    'tradingNames',
                    'type'
                ],
            ],
            [
                'sections' => $this->sectionAccessService->getAccessibleSectionsForLicence($licence),
                'niFlag' => $licence->getNiFlag(),
                'isMlh' => $licence->getOrganisation()->isMlh()
            ]
        );
    }
}
