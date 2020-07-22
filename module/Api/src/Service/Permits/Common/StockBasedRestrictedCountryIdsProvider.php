<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

class StockBasedRestrictedCountryIdsProvider
{
    /** @var StockBasedPermitTypeConfigProvider */
    private $stockBasedPermitTypeConfigProvider;

    /**
     * Create service instance
     *
     * @param StockBasedPermitTypeConfigProvider $stockBasedPermitTypeConfigProvider
     *
     * @return StockBasedRestrictedCountryIdsProvider
     */
    public function __construct(StockBasedPermitTypeConfigProvider $stockBasedPermitTypeConfigProvider)
    {
        $this->stockBasedPermitTypeConfigProvider = $stockBasedPermitTypeConfigProvider;
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
        $permitTypeConfig = $this->stockBasedPermitTypeConfigProvider->getPermitTypeConfig($stockId);
        return $permitTypeConfig->getRestrictedCountryIds();
    }
}
