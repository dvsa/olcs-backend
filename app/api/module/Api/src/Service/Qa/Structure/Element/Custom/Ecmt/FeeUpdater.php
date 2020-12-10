<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Cqrs\CommandCreator;
use Dvsa\Olcs\Api\Service\Permits\Fees\EcmtApplicationFeeCommandCreator;

class FeeUpdater
{
    /** @var CommandCreator */
    private $commandCreator;

    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /** @var EcmtApplicationFeeCommandCreator */
    private $ecmtApplicationFeeCommandCreator;

    /**
     * Create service instance
     *
     * @param CommandCreator $commandCreator
     * @param CommandHandlerManager $commandHandlerManager
     * @param EcmtApplicationFeeCommandCreator $ecmtApplicationFeeCommandCreator
     *
     * @return FeeUpdater
     */
    public function __construct(
        CommandCreator $commandCreator,
        CommandHandlerManager $commandHandlerManager,
        EcmtApplicationFeeCommandCreator $ecmtApplicationFeeCommandCreator
    ) {
        $this->commandCreator = $commandCreator;
        $this->commandHandlerManager = $commandHandlerManager;
        $this->ecmtApplicationFeeCommandCreator = $ecmtApplicationFeeCommandCreator;
    }

    /**
     * Cancel any existing fees as appropriate and create new fees
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitsRequired
     */
    public function updateFees(IrhpApplicationEntity $irhpApplication, $permitsRequired)
    {
        $feeCommands = [];

        $outstandingApplicationFees = $irhpApplication->getOutstandingApplicationFees();

        foreach ($outstandingApplicationFees as $fee) {
            $feeCommands[] = $this->commandCreator->create(
                CancelFee::class,
                ['id' => $fee->getId()]
            );
        }

        $feeCommands[] = $this->ecmtApplicationFeeCommandCreator->create($irhpApplication, $permitsRequired);

        foreach ($feeCommands as $command) {
            $this->commandHandlerManager->handleCommand($command, false);
        }
    }
}
