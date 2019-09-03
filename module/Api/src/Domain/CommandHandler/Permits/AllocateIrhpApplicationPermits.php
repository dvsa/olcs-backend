<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use DateInterval;
use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpApplicationPermits as AllocateIrhpApplicationPermitsCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit as AllocateIrhpPermitApplicationPermitCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use RuntimeException;

/**
 * Allocate permits for an IRHP Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AllocateIrhpApplicationPermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

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
        $irhpApplication = $repo->fetchById($command->getId());
        $repo->refresh($irhpApplication);

        $allocationMode = $irhpApplication->getIrhpPermitType()->getAllocationMode();

        foreach ($irhpApplication->getIrhpPermitApplications() as $irhpPermitApplication) {
            $this->processIrhpPermitApplication($irhpPermitApplication, $allocationMode);
        }

        $irhpApplication->proceedToValid($this->refData(IrhpInterface::STATUS_VALID));
        $repo->save($irhpApplication);

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
            case IrhpPermitType::ALLOCATION_MODE_STANDARD:
                $this->processStandard($irhpPermitApplication);
                break;
            case IrhpPermitType::ALLOCATION_MODE_STANDARD_WITH_EXPIRY:
                $this->processStandardWithExpiry($irhpPermitApplication);
                break;
            case IrhpPermitType::ALLOCATION_MODE_EMISSIONS_CATEGORIES:
                $this->processForEmissionsCategories($irhpPermitApplication);
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
        $command = AllocateIrhpPermitApplicationPermitCmd::create(
            ['id' => $irhpPermitApplication->getId()]
        );

        $this->allocatePermits(
            $command,
            $irhpPermitApplication->getPermitsRequired()
        );
    }

    /**
     * Allocate the permits for an application that uses the standard allocation method with expiry date
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processStandardWithExpiry(IrhpPermitApplication $irhpPermitApplication)
    {
        $expiryInterval = $irhpPermitApplication->getIrhpApplication()->getIrhpPermitType()->getExpiryInterval();
        $expiryDate = new DateTime();
        $expiryDate->add(new DateInterval($expiryInterval));

        $command = AllocateIrhpPermitApplicationPermitCmd::create(
            [
                'id' => $irhpPermitApplication->getId(),
                'expiryDate' => $expiryDate
            ]
        );

        $this->allocatePermits(
            $command,
            $irhpPermitApplication->getPermitsRequired()
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
            $irhpPermitApplication->getId(),
            $irhpPermitApplication->getRequiredEuro5(),
            RefData::EMISSIONS_CATEGORY_EURO5_REF
        );

        $this->processSingleEmissionsCategory(
            $irhpPermitApplication->getId(),
            $irhpPermitApplication->getRequiredEuro6(),
            RefData::EMISSIONS_CATEGORY_EURO6_REF
        );
    }

    /**
     * Allocate the permits for a single emissions category within an application that uses the emissions categories
     * allocation method
     *
     * @param int $irhpPermitApplicationId
     * @param int $permitsRequired
     * @param string $emissionsCategoryId
     */
    private function processSingleEmissionsCategory($irhpPermitApplicationId, $permitsRequired, $emissionsCategoryId)
    {
        $command = AllocateIrhpPermitApplicationPermitCmd::create(
            [
                'id' => $irhpPermitApplicationId,
                'emissionsCategory' => $emissionsCategoryId
            ]
        );

        $this->allocatePermits($command, $permitsRequired);
    }

    /**
     * Run the specified permit allocation command permitsRequired times
     *
     * @param CommandInterface $command
     * @param int $permitsRequired
     */
    private function allocatePermits($command, $permitsRequired)
    {
        for ($index = 0; $index< $permitsRequired; $index++) {
            $this->result->merge(
                $this->handleSideEffect($command)
            );
        }
    }
}
