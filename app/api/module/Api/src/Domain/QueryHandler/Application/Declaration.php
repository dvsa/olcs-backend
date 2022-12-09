<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Declaration extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';
    protected $extraRepos = ['SystemParameter', 'Fee', 'FeeType'];

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
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
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

        $canHaveInterimLicence = $application->canHaveInterimLicence();
        $interimFeeAmount = $canHaveInterimLicence ? $this->getInterimFeeAmount($application) : null;

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
                'canHaveInterimLicence' => $canHaveInterimLicence,
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
                'interimFee' => $interimFeeAmount
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

    /**
     * Get interim fee amount
     *
     * @param ApplicationEntity $application application
     *
     * @return int|null
     */
    protected function getInterimFeeAmount($application)
    {
        $existingFees = $this->getRepo('Fee')->fetchInterimFeesByApplicationId($application->getId(), true);
        if (!empty($existingFees)) {
            return $existingFees[0]->getGrossAmount();
        }

        $trafficArea = null;
        if ($application->getNiFlag() === 'Y') {
            $trafficArea = $this->getRepo()
                ->getReference(TrafficArea::class, TrafficArea::NORTHERN_IRELAND_TRAFFIC_AREA_CODE);
        }

        $feeType = $this->getRepo('FeeType')->fetchLatest(
            $this->getRepo()->getRefdataReference(FeeType::FEE_TYPE_GRANTINT),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            new \DateTime($application->getCreatedOn()),
            $trafficArea,
            true
        );

        if ($feeType !== null && ($application->hasHgvAuthorisationIncreased() ||
            $application->hasLgvAuthorisationIncreased() ||
            $application->hasAuthTrailersIncrease() ||
            $application->hasNewOperatingCentre() ||
            $application->hasIncreaseInOperatingCentre()
          )) {
            return $feeType->getFixedValue() == 0 ? $feeType->getFiveYearValue() : $feeType->getFixedValue();
        }

        return null;
    }
}
