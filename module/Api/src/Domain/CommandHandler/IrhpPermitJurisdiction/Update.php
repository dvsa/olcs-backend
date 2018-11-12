<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitJurisdiction;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitJurisdictionQuota;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\IrhpPermitJurisdiction\Update as UpdateJurisdictionCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitJurisdictionQuota as IrhpPermitJurisdictionQuotaRepo;

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
         * @var IrhpPermitJurisdictionQuotaRepo $repo
         * @var UpdateJurisdictionCmd           $command
         */
        $repo = $this->getRepo('IrhpPermitJurisdictionQuota');

        $quotas = $command->getTrafficAreas();
        $ids = array_keys($quotas);

        $records = $repo->fetchByIds($ids);

        /** @var IrhpPermitJurisdictionQuota $record */
        foreach ($records as $record) {
            $recordId = $record->getId();
            $updatedNumber = $quotas[$recordId];
            $record->update($updatedNumber);

            $repo->save($record);
            $this->result->addId('IrhpPermitJurisdictionQuota', $recordId, true);
        }

        $this->result->addMessage("Irhp Permit Jurisdiction Quota updated");

        return $this->result;
    }
}
