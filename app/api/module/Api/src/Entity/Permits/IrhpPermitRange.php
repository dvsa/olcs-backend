<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\DeletableInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermitRange Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_range",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_ranges_irhp_permit_stocks1_idx",
     *     columns={"irhp_permit_stock_id"}),
 *        @ORM\Index(name="fk_irhp_permit_range_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_range_last_modified_by_user_id",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
class IrhpPermitRange extends AbstractIrhpPermitRange implements DeletableInterface
{
    /**
     * Create
     *
     * @param IrhpPermitStock $permitStock
     * @param RefData $emissionsCategory
     * @param string $prefix
     * @param int $rangeFrom
     * @param int $rangeTo
     * @param int $reserve
     * @param int $replacement
     * @param array $countries
     *
     * @return IrhpPermitRange
     */
    public static function create(
        $permitStock,
        $emissionsCategory,
        $prefix,
        $rangeFrom,
        $rangeTo,
        $reserve,
        $replacement,
        $countries
    ) {
        $instance = new self;

        $instance->irhpPermitStock = $permitStock;
        $instance->emissionsCategory = $emissionsCategory;
        $instance->prefix = $prefix;
        $instance->fromNo = $rangeFrom;
        $instance->toNo = $rangeTo;
        $instance->ssReserve = $reserve;
        $instance->lostReplacement = $replacement;
        $instance->countrys = $countries;

        return $instance;
    }

    /**
     * Update
     *
     * @param IrhpPermitStock $permitStock
     * @param RefData $emissionsCategory
     * @param string $prefix
     * @param int $rangeFrom
     * @param int $rangeTo
     * @param int $reserve
     * @param int $replacement
     * @param array $countries
     *
     * @return IrhpPermitRange
     */
    public function update(
        $permitStock,
        $emissionsCategory,
        $prefix,
        $rangeFrom,
        $rangeTo,
        $reserve,
        $replacement,
        $countries
    ) {
        $this->irhpPermitStock = $permitStock;
        $this->emissionsCategory = $emissionsCategory;
        $this->prefix = $prefix;
        $this->fromNo = $rangeFrom;
        $this->toNo = $rangeTo;
        $this->ssReserve = $reserve;
        $this->lostReplacement = $replacement;
        $this->countrys = $countries;

        return $this;
    }

    /**
     * Checks whether there are dependencies on the Permit Range and returns whether the Permit Range can be deleted.
     *
     * @return boolean
     */
    public function canDelete()
    {
        return
            count($this->irhpCandidatePermits) === 0 &&
            count($this->irhpPermits) === 0 &&
            count($this->countrys) === 0;
    }

    /**
     * Get the number of possible permits in this range
     *
     * @return int
     */
    public function getSize()
    {
        return(($this->toNo - $this->fromNo) + 1);
    }
}
