<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * IrhpPermitStock Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="irhp_permit_stock",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_stock_irhp_permit_types1_idx",
 *     columns={"irhp_permit_type_id"})
 *    }
 * )
 */
class IrhpPermitStock extends AbstractIrhpPermitStock
{
    const STATUS_SCORING_NEVER_RUN = 'stock_scoring_never_run';
    const STATUS_SCORING_PENDING = 'stock_scoring_pending';
    const STATUS_SCORING_IN_PROGRESS = 'stock_scoring_in_progress';
    const STATUS_SCORING_SUCCESSFUL = 'stock_scoring_successful';
    const STATUS_SCORING_PREREQUISITE_FAIL = 'stock_scoring_prereq_fail';
    const STATUS_SCORING_UNEXPECTED_FAIL = 'stock_scoring_unexpected_fail';
    const STATUS_ACCEPT_PENDING = 'stock_accept_pending';
    const STATUS_ACCEPT_IN_PROGRESS = 'stock_accept_in_progress';
    const STATUS_ACCEPT_SUCCESSFUL = 'stock_accept_successful';
    const STATUS_ACCEPT_PREREQUISITE_FAIL = 'stock_accept_prereq_fail';
    const STATUS_ACCEPT_UNEXPECTED_FAIL = 'stock_accept_unexpected_fail';

    public static function create($type, $validFrom, $validTo, $quota, RefData $status)
    {
        $instance = new self;

        $instance->irhpPermitType = $type;
        $instance->validFrom = static::processDate($validFrom, 'Y-m-d');
        $instance->validTo = static::processDate($validTo, 'Y-m-d');
        $instance->initialStock = intval($quota) > 0 ? $quota : 0;
        $instance->status = $status;

        return $instance;
    }

    public function update($type, $validFrom, $validTo, $quota)
    {
        $this->irhpPermitType = $type;
        $this->validFrom = static::processDate($validFrom, 'Y-m-d');
        $this->validTo = static::processDate($validTo, 'Y-m-d');
        $this->initialStock = intval($quota) > 0 ? $quota : 0;

        return $this;
    }

    public function canDelete()
    {
        return
            count($this->irhpPermitRanges) === 0 &&
            count($this->irhpPermitWindows) === 0;
    }

    /**
     * Gets calculated values
     *
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return ['canDelete' => $this->canDelete()];
    }
}
