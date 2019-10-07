<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use RuntimeException;

class StockBasedRestrictedCountryIdsProvider
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var array */
    private $config;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param array $config
     *
     * @return StockBasedRestrictedCountryIdsProvider
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        array $config
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->config = $config;
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
        $typesConfig = $this->config['permits']['types'];

        if (!isset($typesConfig[$irhpPermitTypeId]['restricted_countries'])) {
            throw new RuntimeException('No restricted countries config found for permit type ' . $irhpPermitTypeId);
        }

        return $typesConfig[$irhpPermitTypeId]['restricted_countries'];
    }
}
