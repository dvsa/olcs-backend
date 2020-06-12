<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

class PeriodGenerator
{
    /** @var IrhpPermitStockRepository */
    private $irhpPermitStockRepo;

    /** @var FieldsGenerator */
    private $fieldsGenerator;

    /**
     * Create service instance
     *
     * @param IrhpPermitStockRepository $irhpPermitStockRepo
     * @param FieldsGenerator $fieldsGenerator
     *
     * @return PeriodGenerator
     */
    public function __construct(IrhpPermitStockRepository $irhpPermitStockRepo, FieldsGenerator $fieldsGenerator)
    {
        $this->irhpPermitStockRepo = $irhpPermitStockRepo;
        $this->fieldsGenerator = $fieldsGenerator;
    }

    /**
     * Generate the period part of the response
     *
     * @param int $stockId
     * @param IrhpPermitApplication $irhpPermitApplication (optional)
     *
     * @return array
     */
    public function generate($stockId, ?IrhpPermitApplication $irhpPermitApplication)
    {
        $irhpPermitStock = $this->irhpPermitStockRepo->fetchById($stockId);

        return [
            'id' => $stockId,
            'key' => $irhpPermitStock->getPeriodNameKey(),
            'fields' => $this->fieldsGenerator->generate($irhpPermitStock, $irhpPermitApplication)
        ];
    }
}
