<?php

namespace Dvsa\Olcs\Api\Service\Permits\ApplyRanges;

use Dvsa\Olcs\Api\Service\Permits\Common\StockBasedRestrictedCountryIdsProvider;

class StockBasedForCpProviderFactory
{
    /** @var StockBasedRestrictedCountryIdsProvider */
    private $stockBasedRestrictedCountryIdsProvider;

    /** @var ForCpProviderFactory */
    private $forCpProviderFactory;

    /**
     * Create service instance
     *
     * @param StockBasedRestrictedCountryIdsProvider $stockBasedRestrictedCountryIdsProvider
     * @param ForCpProviderFactory $forCpProviderFactory
     *
     * @return StockBasedForCpProviderFactory
     */
    public function __construct(
        StockBasedRestrictedCountryIdsProvider $stockBasedRestrictedCountryIdsProvider,
        ForCpProviderFactory $forCpProviderFactory
    ) {
        $this->stockBasedRestrictedCountryIdsProvider = $stockBasedRestrictedCountryIdsProvider;
        $this->forCpProviderFactory = $forCpProviderFactory;
    }

    /**
     * Create instance of ForCpProvider corresponding to the specified stock id
     *
     * @param int $irhpPermitStockId
     *
     * @return ForCpProvider
     */
    public function create($irhpPermitStockId)
    {
        return $this->forCpProviderFactory->create(
            $this->stockBasedRestrictedCountryIdsProvider->getIds($irhpPermitStockId)
        );
    }
}
