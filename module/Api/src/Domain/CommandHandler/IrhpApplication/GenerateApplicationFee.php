<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\GenerateApplicationFee as GenerateApplicationFeeCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Generate application fee (if not already present)
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenerateApplicationFee extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['FeeType'];

    /**
     * Handle command
     *
     * @param GenerateApplicationFeeCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        if (!$irhpApplication->canCreateApplicationFee()) {
            throw new ForbiddenException(
                'IRHP application is not in the correct state to allow creation of application fee'
            );
        }

        if ($irhpApplication->hasOutstandingApplicationFee()) {
            $this->result->addMessage('Application fee already exists');
        } else {
            $this->handleSideEffect(
                $this->getCreateApplicationFeeCommand($irhpApplication)
            );
            $this->result->addMessage('Created application fee');
        }

        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * Get application fee creation command for an application
     *
     * @param IrhpApplication $irhpApplication
     *
     * @return CreateFee
     */
    private function getCreateApplicationFeeCommand(IrhpApplication $irhpApplication)
    {
        $feeType = $this->getRepo('FeeType')->getLatestByProductReference(
            FeeType::FEE_TYPE_IRHP_APP_BILATERAL_PRODUCT_REF
        );

        return CreateFee::create(
            [
                'licence' => $irhpApplication->getLicence()->getId(),
                'irhpApplication' => $irhpApplication->getId(),
                'invoicedDate' => date('Y-m-d'),
                'description' => $feeType->getDescription(),
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'amount' => $feeType->getFixedValue()
            ]
        );
    }
}
