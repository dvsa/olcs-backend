<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\NoOfPermitsUpdater;

class IrhpPermitApplicationCreator
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitApplicationFactory */
    private $irhpPermitApplicationFactory;

    /** @var PermitUsageSelectionGenerator */
    private $permitUsageSelectionGenerator;

    /** @var BilateralRequiredGenerator */
    private $bilateralRequiredGenerator;

    /** @var OtherAnswersUpdater */
    private $otherAnswersUpdater;

    /** @var NoOfPermitsUpdater */
    private $noOfPermitsUpdater;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitApplicationFactory $irhpPermitApplicationFactory
     * @param PermitUsageSelectionGenerator $permitUsageSelectionGenerator
     * @param BilateralRequiredGenerator $bilateralRequiredGenerator
     * @param OtherAnswersUpdater $otherAnswersUpdater
     * @param NoOfPermitsUpdater $noOfPermitsUpdater
     *
     * @return IrhpPermitApplicationCreator
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitApplicationFactory $irhpPermitApplicationFactory,
        PermitUsageSelectionGenerator $permitUsageSelectionGenerator,
        BilateralRequiredGenerator $bilateralRequiredGenerator,
        OtherAnswersUpdater $otherAnswersUpdater,
        NoOfPermitsUpdater $noOfPermitsUpdater
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitApplicationFactory = $irhpPermitApplicationFactory;
        $this->permitUsageSelectionGenerator = $permitUsageSelectionGenerator;
        $this->bilateralRequiredGenerator = $bilateralRequiredGenerator;
        $this->otherAnswersUpdater = $otherAnswersUpdater;
        $this->noOfPermitsUpdater = $noOfPermitsUpdater;
    }

    /**
     * Handle the scenario where no irhp permit application exists for a country
     *
     * @param IrhpApplication $irhpApplication
     * @param int $stockId
     * @param array $requiredPermits
     */
    public function create(IrhpApplication $irhpApplication, $stockId, $requiredPermits)
    {
        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);

        $irhpPermitApplication = $this->irhpPermitApplicationFactory->create(
            $irhpApplication,
            $irhpPermitStock->getOpenWindow()
        );
        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);

        $permitUsageSelection = $this->permitUsageSelectionGenerator->generate($requiredPermits);
        $bilateralRequired = $this->bilateralRequiredGenerator->generate($requiredPermits, $permitUsageSelection);

        $this->otherAnswersUpdater->update($irhpPermitApplication, $bilateralRequired, $permitUsageSelection);
        $this->noOfPermitsUpdater->update($irhpPermitApplication, $bilateralRequired);
    }
}
