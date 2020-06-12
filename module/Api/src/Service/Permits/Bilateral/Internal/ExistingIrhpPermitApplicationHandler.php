<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\ApplicationAnswersClearer;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;

class ExistingIrhpPermitApplicationHandler
{
    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var PermitUsageSelectionGenerator */
    private $permitUsageSelectionGenerator;

    /** @var BilateralRequiredGenerator */
    private $bilateralRequiredGenerator;

    /** @var OtherAnswersUpdater */
    private $otherAnswersUpdater;

    /** @var NoOfPermitsUpdater */
    private $noOfPermitsUpdater;

    /** @var ApplicationAnswersClearer */
    private $applicationAnswersClearer;

    /**
     * Create service instance
     *
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param PermitUsageSelectionGenerator $permitUsageSelectionGenerator
     * @param BilateralRequiredGenerator $bilateralRequiredGenerator
     * @param OtherAnswersUpdater $otherAnswersUpdater
     * @param NoOfPermitsUpdater $noOfPermitsUpdater
     * @param ApplicationAnswersClearer $applicationAnswersClearer $applicationAnswersClearer
     *
     * @return IrhpPermitApplicationCreator
     */
    public function __construct(
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitStockRepository $irhpPermitStockRepo,
        PermitUsageSelectionGenerator $permitUsageSelectionGenerator,
        BilateralRequiredGenerator $bilateralRequiredGenerator,
        OtherAnswersUpdater $otherAnswersUpdater,
        NoOfPermitsUpdater $noOfPermitsUpdater,
        ApplicationAnswersClearer $applicationAnswersClearer
    ) {
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->permitUsageSelectionGenerator = $permitUsageSelectionGenerator;
        $this->bilateralRequiredGenerator = $bilateralRequiredGenerator;
        $this->otherAnswersUpdater = $otherAnswersUpdater;
        $this->noOfPermitsUpdater = $noOfPermitsUpdater;
        $this->applicationAnswersClearer = $applicationAnswersClearer;
    }

    /**
     * Handle the scenario where an irhp permit application already exists for a country
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     * @param int $stockId
     * @param array $requiredPermits
     */
    public function handle(IrhpPermitApplication $irhpPermitApplication, $stockId, $requiredPermits)
    {
        $existingStockId = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock()->getId();
        $existingPermitUsageSelection = $irhpPermitApplication->getBilateralPermitUsageSelection();
        $existingBilateralRequired = $irhpPermitApplication->getBilateralRequired();

        $permitUsageSelection = $this->permitUsageSelectionGenerator->generate($requiredPermits);
        $bilateralRequired = $this->bilateralRequiredGenerator->generate($requiredPermits, $permitUsageSelection);

        if (($stockId == $existingStockId) &&
            ($permitUsageSelection == $existingPermitUsageSelection) &&
            ($bilateralRequired == $existingBilateralRequired)
        ) {
            $irhpPermitApplication->updateCheckAnswers();
            $this->irhpPermitApplicationRepo->save($irhpPermitApplication);
            return;
        }

        $this->applicationAnswersClearer->clear($irhpPermitApplication);

        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);
        $irhpPermitApplication->updateIrhpPermitWindow(
            $irhpPermitStock->getOpenWindow()
        );

        $this->otherAnswersUpdater->update($irhpPermitApplication, $bilateralRequired, $permitUsageSelection);
        $this->noOfPermitsUpdater->update($irhpPermitApplication, $bilateralRequired);
    }
}
