<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitSector;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\IrhpPermitSector\Update as UpdateSectorCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Update an IRHP Permit Sector
 *
 * @author Scott Callaway
 */
final class Update extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitSectorQuota';

    /**
     * Handle Command
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateSectorCmd $command
         */
        $irhpPermitStockId = $command->getIrhpPermitStock();

        foreach ($command->getSectors() as $index => $sector) {
            $this->getRepo()->updateSectorPermitQuantity($sector, $index, $irhpPermitStockId);
        }

        $this->result->addId('Irhp Permit Stock', $irhpPermitStockId);
        $this->result->addMessage("Irhp Permit Sectors for Stock '{$irhpPermitStockId}' updated");

        return $this->result;
    }
}
