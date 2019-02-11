<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Abstract create fee (or replace if already present)
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
abstract class AbstractRegenerateFee extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['FeeType'];

    /** @var string The product reference from the FeeType table */
    protected $productReference = 'changeMe';

    /** @var string The human-readable name of the fee */
    protected $feeName = 'changeMe';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        if (!$this->canCreateOrReplaceFee($irhpApplication)) {
            throw new ForbiddenException(
                'IRHP application is not in the correct state to allow create/replace of ' . $this->feeName
            );
        }

        $feeCommands = [];

        $fee = $this->getLatestOutstandingFee($irhpApplication);
        if (!is_null($fee)) {
            $feeCommands[] = CancelFee::create(['id' => $fee->getId()]);
            $this->result->addMessage('Cancelled existing ' . $this->feeName);
        }

        $feeCommands[] = $this->getCreateFeeCommand($irhpApplication);
        $this->result->addMessage('Created new ' . $this->feeName);

        $this->result->merge(
            $this->handleSideEffects($feeCommands)
        );

        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Get fee creation command for an application
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return CreateFee
     */
    private function getCreateFeeCommand(IrhpApplication $irhpApplication)
    {
        $feeType = $this->getRepo('FeeType')->getLatestByProductReference($this->productReference);

        $permitsRequired = $irhpApplication->getPermitsRequired();

        $feeDescription = sprintf(
            '%s - %d permits',
            $feeType->getDescription(),
            $permitsRequired
        );

        return CreateFee::create(
            [
                'licence' => $irhpApplication->getLicence()->getId(),
                'irhpApplication' => $irhpApplication->getId(),
                'invoicedDate' => date('Y-m-d'),
                'description' => $feeDescription,
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => $feeType->getFixedValue() * $permitsRequired
            ]
        );
    }

    /**
     * Whether a fee can be created or replaced
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return bool
     */
    abstract protected function canCreateOrReplaceFee(IrhpApplication $irhpApplication);

    /**
     * Get the latest outstanding fee
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return Fee|null
     */
    abstract protected function getLatestOutstandingFee(IrhpApplication $irhpApplication);
}
