<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Application extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionConfig
     */
    private $sectionConfig;

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionConfig = $mainServiceLocator->get('SectionConfig');
        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');
        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [
                'licence'
            ],
            [
                'sections' => $this->getSections($application)
            ]
        );
    }

    protected function getSections(ApplicationEntity $application)
    {
        $lva = $application->isVariation() ? 'variation' : 'application';
        $location = $this->isGranted(Permission::INTERNAL_USER) ? 'internal' : 'external';

        $hasConditions = $application->getLicence()->hasApprovedUnfulfilledConditions();

        $goodsOrPsv = null;
        if ($application->getGoodsOrPsv() !== null) {
            $goodsOrPsv = $application->getGoodsOrPsv()->getId();
        }

        $licenceType = null;
        if ($application->getLicenceType() !== null) {
            $licenceType = $application->getLicenceType()->getId();
        }

        $access = [
            $location,
            $lva,
            $goodsOrPsv,
            $licenceType,
            $hasConditions ? 'hasConditions' : 'noConditions'
        ];

        if ($lva === 'variation') {
            $this->sectionConfig->setVariationCompletion($application->getApplicationCompletion());
        }

        $inputSections = $this->sectionConfig->getAll();

        return $this->sectionAccessService->setSections($inputSections)->getAccessibleSections($access);
    }
}
