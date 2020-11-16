<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;

class IrhpPermitApplicationCreator
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var IrhpPermitApplicationRepository */
    private $irhpPermitApplicationRepo;

    /** @var IrhpPermitApplicationFactory */
    private $irhpPermitApplicationFactory;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param IrhpPermitApplicationRepository $irhpPermitApplicationRepo
     * @param IrhpPermitApplicationFactory $irhpPermitApplicationFactory
     *
     * @return IrhpPermitApplicationCreator
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        IrhpPermitApplicationRepository $irhpPermitApplicationRepo,
        IrhpPermitApplicationFactory $irhpPermitApplicationFactory
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->irhpPermitApplicationRepo = $irhpPermitApplicationRepo;
        $this->irhpPermitApplicationFactory = $irhpPermitApplicationFactory;
    }

    /**
     * Handle the scenario where no irhp permit application exists for a country
     *
     * @param IrhpApplication $irhpApplication
     * @param int $stockId
     */
    public function create(IrhpApplication $irhpApplication, $stockId)
    {
        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);

        $irhpPermitApplication = $this->irhpPermitApplicationFactory->create(
            $irhpApplication,
            $irhpPermitStock->getOpenWindow()
        );

        $this->irhpPermitApplicationRepo->save($irhpPermitApplication);

        return $irhpPermitApplication;
    }
}
