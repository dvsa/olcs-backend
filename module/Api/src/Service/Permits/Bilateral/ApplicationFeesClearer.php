<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Cqrs\CommandCreator;

class ApplicationFeesClearer
{
    /** @var CommandCreator */
    private $commandCreator;

    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /** @var FeeRepository */
    private $feeRepo;

    /**
     * Create service instance
     *
     * @param CommandCreator $commandCreator
     * @param CommandHandlerManager $commandHandlerManager
     * @param FeeRepository $feeRepo
     *
     * @return ApplicationFeesClearer
     */
    public function __construct(
        CommandCreator $commandCreator,
        CommandHandlerManager $commandHandlerManager,
        FeeRepository $feeRepo
    ) {
        $this->commandCreator = $commandCreator;
        $this->commandHandlerManager = $commandHandlerManager;
        $this->feeRepo = $feeRepo;
    }

    /**
     * Cancel all outstanding fees associated with this application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    public function clear(IrhpPermitApplication $irhpPermitApplication)
    {
        $outstandingFees = $irhpPermitApplication->getOutstandingFees();
        foreach ($outstandingFees as $fee) {
            $command = $this->commandCreator->create(
                CancelFee::class,
                ['id' => $fee->getId()]
            );
            $this->commandHandlerManager->handleCommand($command, false);
        }

        $fees = $irhpPermitApplication->getFees();
        foreach ($fees as $fee) {
            $fee->removeIrhpPermitApplicationAssociation();
            $this->feeRepo->save($fee);
        }
    }
}
