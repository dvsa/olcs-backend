<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermitWindow Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_window",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_windows_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"}),
 *        @ORM\Index(name="fk_irhp_permit_window_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_window_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitWindow extends AbstractIrhpPermitWindow
{
    const EMISSIONS_CATEGORY_EURO6_REF =    'emissions_cat_euro6';
    const EMISSIONS_CATEGORY_EURO5_REF =    'emissions_cat_euro5';
    const EMISSIONS_CATEGORY_NA_REF =       'emissions_cat_na';

    /**
     * @param IrhpPermitStock $irhpPermitStock
     * @param RefData $emissionsCategory
     * @param string $startDate
     * @param string $endDate
     * @param int $daysForPayment
     * @return IrhpPermitWindow
     */
    public static function create($irhpPermitStock, $emissionsCategory, $startDate, $endDate, $daysForPayment)
    {
        $instance = new self;

        $instance->irhpPermitStock = $irhpPermitStock;
        $instance->emissionsCategory = $emissionsCategory;
        $instance->startDate = new DateTime($startDate);
        $instance->endDate = new DateTime($endDate);
        $instance->daysForPayment = intval($daysForPayment) > 0 ? $daysForPayment : 0;

        return $instance;
    }

    /**
     * @param IrhpPermitStock $irhpPermitStock
     * @param RefData $emissionsCategory
     * @param string $startDate
     * @param string $endDate
     * @param int $daysForPayment
     * @return $this
     */
    public function update($irhpPermitStock, $emissionsCategory, $startDate, $endDate, $daysForPayment)
    {
        $this->irhpPermitStock = $irhpPermitStock;
        $this->emissionsCategory = $emissionsCategory;
        $this->startDate = new DateTime($startDate);
        $this->endDate = new DateTime($endDate);
        $this->daysForPayment = intval($daysForPayment) > 0 ? $daysForPayment : 0;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEnded()
    {
        $today = new DateTime();
        $endDate = new DateTime($this->endDate);
        return($today->getTimestamp() > $endDate->getTimestamp());
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $today = new DateTime();
        $startDate = new DateTime($this->startDate);
        $endDate = new DateTime($this->endDate);
        return($today->getTimestamp() >= $startDate->getTimestamp()
            && $today->getTimestamp() <= $endDate->getTimeStamp());
    }

    /**
     * @return bool
     */
    public function canBeDeleted()
    {
        return !($this->hasEnded() || $this->isActive());
    }
}
