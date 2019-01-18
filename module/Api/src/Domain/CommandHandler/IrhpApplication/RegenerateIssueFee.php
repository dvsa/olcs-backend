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
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\RegenerateIssueFee as RegenerateIssueFeeCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create issue fee (or replace if already present)
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RegenerateIssueFee extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['FeeType'];

    /**
     * Handle command
     *
     * @param RegenerateIssueFeeCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        if (!$irhpApplication->canCreateOrReplaceIssueFee()) {
            throw new ForbiddenException(
                'IRHP application is not in the correct state to allow create/replace of issue fee'
            );
        }

        $feeCommands = [];

        $issueFee = $irhpApplication->getLatestOutstandingIssueFee();
        if (!is_null($issueFee)) {
            $feeCommands[] = CancelFee::create(['id' => $issueFee->getId()]);
            $this->result->addMessage('Cancelled existing issue fee');
        }

        $feeCommands[] = $this->getCreateIssueFeeCommand($irhpApplication);
        $this->result->addMessage('Created new issue fee');

        $this->result->merge(
            $this->handleSideEffects($feeCommands)
        );

        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Get issue fee creation command for an application
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return CreateFee
     */
    private function getCreateIssueFeeCommand(IrhpApplication $irhpApplication)
    {
        $feeType = $this->getRepo('FeeType')->getLatestByProductReference(
            FeeType::FEE_TYPE_IRHP_ISSUE_BILATERAL_PRODUCT_REF
        );

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
}
