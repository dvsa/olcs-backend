<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;
use Olcs\Logging\Log\Logger;

/**
 * Update Fees when saving number of permits field from relevant SelfServe or Internal command handlers
 *
 * @author Andy Newton <andrew.newton@capgemini.com>
 */
final class UpdatePermitFee extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['Fee', 'FeeType'];

    public function handleCommand(CommandInterface $command)
    {
        $createNew = false;
        // Get outstanding fees for this permit application
        $fees = $this->getRepo('Fee')->fetchFeeByEcmtPermitApplicationId($command->getEcmtPermitApplicationId());
        // if no outstanding fees, create one for the number of permits requested
        if (empty($fees)) {
            $this->createApplicationFee($command);
        } else {
            // Outstanding fee(s) detected, cancel them ans then create a new one with new permitsRequired value.
            foreach ($fees as $fee) {
                if ($fee->isOutstanding()) {
                    $this->result->merge($this->handleSideEffect(CancelFee::create(['id' => $fee->getId()])));
                    $createNew = true;
                }
            }
            if ($createNew) {
                $this->createApplicationFee($command);
            }
        }
        return $this->result;
    }


    protected function createApplicationFee($command)
    {
        $feeType = $this->getRepo('FeeType')->getSpecificDateEcmtPermit(FeeType::FEE_TYPE_ECMT_APP_PRODUCT_REF, $command->getReceivedDate());

        $data = [
            'licence' => $command->getLicenceId(),
            'ecmtPermitApplication' => $command->getEcmtPermitApplicationId(),
            'invoicedDate' => date('Y-m-d'),
            'description' => $feeType->getDescription() . ' - ' . $command->getPermitsRequired() . ' permits',
            'feeType' => $feeType->getId(),
            'feeStatus' => Fee::STATUS_OUTSTANDING,
            'amount' => $feeType->getFixedValue(),
            'quantity' => $command->getPermitsRequired(),
        ];
        $this->result->merge($this->handleSideEffect(CreateFee::create($data)));
    }
}
