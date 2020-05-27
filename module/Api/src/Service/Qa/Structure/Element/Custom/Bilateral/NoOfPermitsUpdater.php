<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Cqrs\CommandCreator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\ApplicationFeesClearer;
use Dvsa\Olcs\Api\Service\Common\CurrentDateTimeFactory;

class NoOfPermitsUpdater
{
    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var FeeTypeRepository */
    private $feeTypeRepo;

    /** @var CommandCreator */
    private $commandCreator;

    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /** @var ApplicationFeesClearer */
    private $applicationFeesClearer;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param FeeTypeRepository $feeTypeRepo
     * @param CommandCreator $commandCreator
     * @param CommandHandlerManager $commandHandlerManager
     * @param ApplicationFeesClearer $applicationFeesClearer
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     *
     * @return NoOfPermitsUpdater
     */
    public function __construct(
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        FeeTypeRepository $feeTypeRepo,
        CommandCreator $commandCreator,
        CommandHandlerManager $commandHandlerManager,
        ApplicationFeesClearer $applicationFeesClearer,
        CurrentDateTimeFactory $currentDateTimeFactory
    ) {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->feeTypeRepo = $feeTypeRepo;
        $this->commandCreator = $commandCreator;
        $this->commandHandlerManager = $commandHandlerManager;
        $this->applicationFeesClearer = $applicationFeesClearer;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
    }

    /**
     * Update the answers and fees relating to a specific country within a bilateral application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param array $updatedAnswers
     */
    public function update(IrhpPermitApplication $irhpPermitApplication, array $updatedAnswers)
    {
        $irhpPermitApplication->updateBilateralRequired($updatedAnswers);
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
        $this->applicationFeesClearer->clear($irhpPermitApplication);

        $currentDateTime = $this->currentDateTimeFactory->create();
        $irhpApplication = $irhpPermitApplication->getIrhpApplication();

        $createFeeParameters = [
            'licence' => $irhpApplication->getLicence()->getId(),
            'irhpApplication' => $irhpApplication->getId(),
            'irhpPermitApplication' => $irhpPermitApplication->getId(),
            'invoicedDate' => $currentDateTime->format('Y-m-d'),
            'feeStatus' => Fee::STATUS_OUTSTANDING,
        ];

        $productRefsAndQuantities = $irhpPermitApplication->getBilateralFeeProductRefsAndQuantities();
        foreach ($productRefsAndQuantities as $productReference => $quantity) {
            $feeType = $this->feeTypeRepo->getLatestByProductReference($productReference);

            $createFeeParameters['feeType'] = $feeType->getId();
            $createFeeParameters['quantity'] = $quantity;

            $command = $this->commandCreator->create(CreateFee::class, $createFeeParameters);
            $this->commandHandlerManager->handleCommand($command, false);
        }
    }
}
