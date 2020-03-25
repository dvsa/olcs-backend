<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Cqrs\CommandCreator;

class FeeUpdater
{
    /** @var FeeTypeRepository */
    private $feeTypeRepo;

    /** @var CommandCreator */
    private $commandCreator;

    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /**
     * Create service instance
     *
     * @param FeeTypeRepository $feeTypeRepo
     * @param CommandCreator $commandCreator
     * @param CommandHandlerManager $commandHandlerManager
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     *
     * @return FeeCreator
     */
    public function __construct(
        FeeTypeRepository $feeTypeRepo,
        CommandCreator $commandCreator,
        CommandHandlerManager $commandHandlerManager,
        CurrentDateTimeFactory $currentDateTimeFactory
    ) {
        $this->feeTypeRepo = $feeTypeRepo;
        $this->commandCreator = $commandCreator;
        $this->commandHandlerManager = $commandHandlerManager;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
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

        $feeType = $this->feeTypeRepo->getLatestByProductReference(
            $irhpApplication->getApplicationFeeProductReference()
        );

        $feeDescription = sprintf(
            '%s - %d permits',
            $feeType->getDescription(),
            $permitsRequired
        );

        $currentDateTime = $this->currentDateTimeFactory->create();

        $feeCommands[] = $this->commandCreator->create(
            CreateFee::class,
            [
                'licence' => $irhpApplication->getLicence()->getId(),
                'irhpApplication' => $irhpApplication->getId(),
                'invoicedDate' => $currentDateTime->format('Y-m-d'),
                'description' => $feeDescription,
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'quantity' => $permitsRequired
            ]
        );

        foreach ($feeCommands as $command) {
            $this->commandHandlerManager->handleCommand($command, false);
        }
    }
}
