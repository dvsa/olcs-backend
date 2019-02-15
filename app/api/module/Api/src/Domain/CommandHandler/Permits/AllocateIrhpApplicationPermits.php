<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpApplicationPermits as AllocateIrhpApplicationPermitsCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit as AllocateIrhpPermitApplicationPermitCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Allocate permits for an IRHP Permit application
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class AllocateIrhpApplicationPermits extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

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

        $irhpApplication = $this->getRepo()->fetchById($command->getId());
        foreach ($irhpApplication->getIrhpPermitApplications() as $irhpPermitApplication) {
            $this->processIrhpPermitApplication($irhpPermitApplication);
        }

        $irhpApplication->proceedToValid($this->refData(IrhpInterface::STATUS_VALID));
        $this->getRepo()->save($irhpApplication);

        $this->result->addMessage('Allocated requested permits for IRHP application');
        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Allocate the permits relating to a given irhp permit application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    private function processIrhpPermitApplication(IrhpPermitApplication $irhpPermitApplication)
    {
        $command = AllocateIrhpPermitApplicationPermitCmd::create(
            ['id' => $irhpPermitApplication->getId()]
        );

        $permitsRequired = $irhpPermitApplication->getPermitsRequired();
        for ($index = 0; $index < $permitsRequired; $index++) {
            $this->result->merge(
                $this->handleSideEffect($command)
            );
        }
    }
}
