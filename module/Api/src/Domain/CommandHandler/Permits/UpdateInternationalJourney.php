<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateInternationalJourney as UpdateInternationalJourneyCmd;

/**
 * Create an ECMT Permit application
 *
 * @author Jason de Jonge
 */
final class UpdateInternationalJourney extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var EcmtPermitApplication         $application
         * @var UpdateInternationalJourneyCmd $command
         */
        $application = $this->getRepo()->fetchById($command->getId());

        $internationalJourneyRefData = $this->refData($command->getInternationalJourney());
        $application->updateInternationalJourneys($internationalJourneyRefData);

        $this->getRepo()->save($application);

        $result->addId('ecmtPermitApplication', $application->getId());

        return $result;
    }
}
