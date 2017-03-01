<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

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
        $this->reviewService = $mainServiceLocator->get('Review\ApplicationUndertakings');

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
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        $signatureDetails = [];
        if ($application->getDigitalSignature()) {
            $signatureDetails = [
                'name' => $application->getDigitalSignature()->getSignatureName(),
                'date' => $application->getDigitalSignature()->getCreatedOn(),
                'dob' => $application->getDigitalSignature()->getDateOfBirth(),
            ];
        }

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
                    (bool)$this->getRepo('SystemParameter')->fetchValue(SystemParameter::DISABLE_GDS_VERIFY_SIGNATURES),
                'declarations' => $this->getDeclarations($application),
                'signature' => $signatureDetails,
            ]
        );
    }

    /**
     * Get declarations
     *
     * @param ApplicationEntity $application application
     *
     * @return string
     */
    protected function getDeclarations($application)
    {
        $data = $application->serialize();
        $data['isGoods'] = $application->isGoods();
        $data['isInternal'] = false;

        return $this->reviewService->getMarkup($data);
    }
}
