<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitJurisdiction;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\IrhpPermitJurisdiction\Update as UpdateJurisdictionCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Update an IRHP Permit Jurisdiction
 *
 * @author Scott Callaway
 */
final class Update extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitJurisdictionQuota';

    /**
     * Handle Command
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateJurisdictionCmd $command
         */
        $irhpPermitStockId = $command->getIrhpPermitStock();

        foreach ($command->getTrafficAreas() as $trafficArea => $quotaNumber) {
            $this->getRepo()->updateTrafficAreaPermitQuantity($quotaNumber, $trafficArea, $irhpPermitStockId);
        }

        $this->result->addId('Irhp Permit Stock', $irhpPermitStockId);
        $this->result->addMessage("Irhp Permit Jurisdiction for Stock '{$irhpPermitStockId}' updated");

        return $this->result;
    }
}
