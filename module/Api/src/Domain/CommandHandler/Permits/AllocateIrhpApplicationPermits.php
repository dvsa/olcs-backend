<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateCandidatePermits as AllocateCandidatePermitsCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpApplicationPermits as AllocateIrhpApplicationPermitsCmd;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Allocate\BilateralCriteriaFactory;
use Dvsa\Olcs\Api\Service\Permits\Allocate\EmissionsStandardCriteriaFactory;
use Dvsa\Olcs\Api\Service\Permits\Allocate\IrhpPermitAllocator;
use Dvsa\Olcs\Api\Service\Permits\Allocate\RangeMatchingCriteriaInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Allocate permits for an IRHP Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AllocateIrhpApplicationPermits extends AbstractCommandHandler
{
    use QueueAwareTrait;

    protected $repoServiceName = 'IrhpApplication';

    /** @var EmissionsStandardCriteriaFactory */
    private $emissionsStandardCriteriaFactory;

    /** @var BilateralCriteriaFactory */
    private $bilateralCriteriaFactory;

    /** @var IrhpPermitAllocator */
    private $irhpPermitAllocator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->emissionsStandardCriteriaFactory = $mainServiceLocator->get(
            'PermitsAllocateEmissionsStandardCriteriaFactory'
        );
        $this->bilateralCriteriaFactory = $mainServiceLocator->get('PermitsAllocateBilateralCriteriaFactory');
        $this->irhpPermitAllocator = $mainServiceLocator->get('PermitsAllocateIrhpPermitAllocator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param AllocateIrhpApplicationPermitsCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();

        $repo = $this->getRepo();
        $irhpApplication = $repo->fetchById($irhpApplicationId);
        $repo->refresh($irhpApplication);

        $irhpPermitApplications = $irhpApplication->getIrhpPermitApplications();

        $allocationMode = $irhpPermitApplications->first()
            ->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getAllocationMode();

        foreach ($irhpPermitApplications as $irhpPermitApplication) {
            $this->processIrhpPermitApplication($irhpPermitApplication, $allocationMode);
        }

        $irhpApplication->proceedToValid($this->refData(IrhpInterface::STATUS_VALID));
        $repo->save($irhpApplication);

        $issuedEmailCommand = $irhpApplication->getIssuedEmailCommand();
        if ($issuedEmailCommand) {
            $this->result->merge(
                $this->handleSideEffect(
                    $this->emailQueue(
                        $issuedEmailCommand,
                        [ 'id' => $irhpApplicationId ],
                        $irhpApplicationId
                    )
                )
            );
        }

        $this->result->addMessage('Allocated requested permits for IRHP application');
        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Allocate the permits relating to a given irhp permit application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param string $allocationMode
     */
    private function processIrhpPermitApplication(IrhpPermitApplication $irhpPermitApplication, $allocationMode)
    {
        switch ($allocationMode) {
            case IrhpPermitStock::ALLOCATION_MODE_STANDARD:
                $this->processStandard($irhpPermitApplication);
                break;
            case IrhpPermitStock::ALLOCATION_MODE_STANDARD_WITH_EXPIRY:
                $this->processStandardWithExpiry($irhpPermitApplication);
                break;
            case IrhpPermitStock::ALLOCATION_MODE_EMISSIONS_CATEGORIES:
                $this->processForEmissionsCategories($irhpPermitApplication);
                break;
            case IrhpPermitStock::ALLOCATION_MODE_CANDIDATE_PERMITS:
                $this->processForCandidatePermits($irhpPermitApplication);
                break;
            case IrhpPermitStock::ALLOCATION_MODE_BILATERAL:
                $this->processForBilateral($irhpPermitApplication);
                break;
            default:
                throw new RuntimeException('Unknown allocation mode: ' . $allocationMode);
        }
    }

    /**
     * Allocate the permits for an application that uses the standard allocation method
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processStandard(IrhpPermitApplication $irhpPermitApplication)
    {
        $this->allocatePermits(
            $irhpPermitApplication,
            null,
            $irhpPermitApplication->countPermitsRequired()
        );
    }

    /**
     * Allocate the permits for an application that uses the standard allocation method with expiry date
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processStandardWithExpiry(IrhpPermitApplication $irhpPermitApplication)
    {
        $this->allocatePermits(
            $irhpPermitApplication,
            null,
            $irhpPermitApplication->countPermitsRequired(),
            $irhpPermitApplication->generateExpiryDate()
        );
    }

    /**
     * Allocate the permits for an application that uses the emissions categories allocation method
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processForEmissionsCategories(IrhpPermitApplication $irhpPermitApplication)
    {
        $this->processSingleEmissionsCategory(
            $irhpPermitApplication,
            $irhpPermitApplication->getRequiredEuro5(),
            RefData::EMISSIONS_CATEGORY_EURO5_REF
        );

        $this->processSingleEmissionsCategory(
            $irhpPermitApplication,
            $irhpPermitApplication->getRequiredEuro6(),
            RefData::EMISSIONS_CATEGORY_EURO6_REF
        );
    }

    /**
     * Allocate the permits based upon the candidate permits associated with the irhp permit application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processForCandidatePermits(IrhpPermitApplication $irhpPermitApplication)
    {
        $command = AllocateCandidatePermitsCmd::create(
            ['id' => $irhpPermitApplication->getId()]
        );

        $this->result->merge(
            $this->handleSideEffect($command)
        );
    }

    /**
     * Allocate the permits for a bilateral application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processForBilateral(IrhpPermitApplication $irhpPermitApplication)
    {
        $bilateralRequired = $irhpPermitApplication->getFilteredBilateralRequired();
        $permitUsage = $irhpPermitApplication->getBilateralPermitUsageSelection();

        foreach ($bilateralRequired as $standardOrCabotage => $permitsRequired) {
            $criteria = $this->bilateralCriteriaFactory->create($standardOrCabotage, $permitUsage);
            $this->allocatePermits($irhpPermitApplication, $criteria, $permitsRequired);
        }
    }

    /**
     * Allocate the permits for a single emissions category within an application that uses the emissions categories
     * allocation method
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param int $permitsRequired
     * @param string $emissionsCategoryId
     */
    private function processSingleEmissionsCategory(IrhpPermitApplication $irhpPermitApplication, $permitsRequired, $emissionsCategoryId)
    {
        $criteria = $this->emissionsStandardCriteriaFactory->create($emissionsCategoryId);

        $this->allocatePermits($irhpPermitApplication, $criteria, $permitsRequired);
    }

    /**
     * Run the specified permit allocation command permitsRequired times
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param RangeMatchingCriteriaInterface $criteria (optional)
     * @param int $permitsRequired
     * @param DateTime $expiryDate (optional)
     */
    private function allocatePermits(
        IrhpPermitApplication $irhpPermitApplication,
        ?RangeMatchingCriteriaInterface $criteria,
        $permitsRequired,
        $expiryDate = null
    ) {
        for ($index = 0; $index < $permitsRequired; $index++) {
            $this->irhpPermitAllocator->allocate($this->result, $irhpPermitApplication, $criteria, $expiryDate);
        }
    }
}
