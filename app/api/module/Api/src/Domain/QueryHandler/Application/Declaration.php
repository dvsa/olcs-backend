<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Declaration extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';
    protected $extraRepos = ['SystemParameter'];

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator service locator
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');

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
        /* @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [
                'licence' => [
                    'organisation' => [
                        'type'
                    ]
                ],
                'applicationCompletion',
            ],
            [
                'canHaveInterimLicence' => $application->canHaveInterimLicence(),
                'isLicenceUpgrade' => $application->isLicenceUpgrade(),
                'outstandingFeeTotal' => $this->feesHelper->getTotalOutstandingFeeAmountForApplication(
                    $application->getId()
                ),
                'sections' => $this->sectionAccessService->getAccessibleSections($application),
                'variationCompletion' => $application->getVariationCompletion(),
                'disableSignatures' =>
                    $this->getRepo('SystemParameter')->fetchValue(SystemParameter::DISABLE_GDS_VERIFY_SIGNATURES),
            ]
        );
    }
}
