<?php

namespace Dvsa\Olcs\Api\Service\Permits\Common;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;

class StockBasedPermitTypeConfigProvider
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var TypeBasedPermitTypeConfigProvider */
    private $typeBasedPermitTypeConfigProvider;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param TypeBasedPermitTypeConfigProvider $typeBasedPermitTypeConfigProvider
     *
     * @return StockBasedPermitTypeConfigProvider
     */
    public function __construct(
        IrhpPermitStockRepository $irhpPermitStockRepo,
        TypeBasedPermitTypeConfigProvider $typeBasedPermitTypeConfigProvider
    ) {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->typeBasedPermitTypeConfigProvider = $typeBasedPermitTypeConfigProvider;
    }

    /**
     * Return the permit type config corresponding to the specified stock id
     *
     * @param int $stockId
     *
     * @return PermitTypeConfig
     */
    public function getPermitTypeConfig($stockId)
    {
        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);
        $irhpPermitTypeId = $irhpPermitStock->getIrhpPermitType()->getId();

        return $this->typeBasedPermitTypeConfigProvider->getPermitTypeConfig($irhpPermitTypeId);
    }
}
