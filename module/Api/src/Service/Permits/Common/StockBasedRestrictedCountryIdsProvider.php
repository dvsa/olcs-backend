<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Service\Permits\Common\TypeBasedRestrictedCountriesProvider;

class StockBasedRestrictedCountryIdsProvider
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var TypeBasedRestrictedCountriesProvider */
    private $typeBasedRestrictedCountriesProvider;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param TypeBasedRestrictedCountriesProvider $typeBasedRestrictedCountriesProvider
     *
     * @return StockBasedRestrictedCountryIdsProvider
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        TypeBasedRestrictedCountriesProvider $typeBasedRestrictedCountriesProvider
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->typeBasedRestrictedCountriesProvider = $typeBasedRestrictedCountriesProvider;
    }

    /**
     * Return the restricted country codes corresponding to the specified stock id
     *
     * @param int $stockId
     *
     * @return array
     */
    public function getIds($stockId)
    {
        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);
        $irhpPermitTypeId = $irhpPermitStock->getIrhpPermitType()->getId();

        return $this->typeBasedRestrictedCountriesProvider->getIds($irhpPermitTypeId);
    }
}
