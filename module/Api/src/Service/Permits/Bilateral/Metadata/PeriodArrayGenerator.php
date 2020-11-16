<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Common\CurrentDateTimeFactory;

class PeriodArrayGenerator
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var PeriodGenerator */
    private $periodGenerator;

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param PeriodGenerator $periodGenerator
     * @param CurrentDateTimeFactory $currentDateTimeFactory
     *
     * @return PeriodArrayGenerator
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        PeriodGenerator $periodGenerator,
        CurrentDateTimeFactory $currentDateTimeFactory
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->periodGenerator = $periodGenerator;
        $this->currentDateTimeFactory = $currentDateTimeFactory;
    }

    /**
     * Generate the period list part of the response
     *
     * @param string $behaviour
     * @param Country $country
     * @param IrhpPermitApplication $irhpPermitApplication (optional)
     *
     * @return array
     */
    public function generate($behaviour, Country $country, ?IrhpPermitApplication $irhpPermitApplication)
    {
        $stocks = $this->irhpPermitStockRepo->fetchOpenBilateralStocksByCountry(
            $country->getId(),
            $this->currentDateTimeFactory->create()
        );

        $responsePeriods = [];

        foreach ($stocks as $stock) {
            $responsePeriods[] = $this->periodGenerator->generate($stock['id'], $behaviour, $irhpPermitApplication);
        }

        return $responsePeriods;
    }
}
