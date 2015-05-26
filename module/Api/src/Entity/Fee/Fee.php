<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Fee Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="fee",
 *    indexes={
 *        @ORM\Index(name="ix_fee_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_fee_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_fee_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_fee_task_id", columns={"task_id"}),
 *        @ORM\Index(name="ix_fee_fee_type_id", columns={"fee_type_id"}),
 *        @ORM\Index(name="ix_fee_parent_fee_id", columns={"parent_fee_id"}),
 *        @ORM\Index(name="ix_fee_waive_recommender_user_id", columns={"waive_recommender_user_id"}),
 *        @ORM\Index(name="ix_fee_waive_approver_user_id", columns={"waive_approver_user_id"}),
 *        @ORM\Index(name="ix_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_fee_fee_status", columns={"fee_status"}),
 *        @ORM\Index(name="ix_fee_payment_method", columns={"payment_method"})
 *    }
 * )
 */
class Fee extends AbstractFee
{
    const STATUS_OUTSTANDING = 'lfs_ot';
    const STATUS_PAID = 'lfs_pd';
    const STATUS_WAIVE_RECOMMENDED = 'lfs_wr';
    const STATUS_WAIVED = 'lfs_w';
    const STATUS_CANCELLED = 'lfs_cn';

    public function __construct(FeeType $feeType, $amount, RefData $feeStatus)
    {
        parent::__construct();

        $this->feeType = $feeType;
        $this->amount = $amount;
        $this->feeStatus = $feeStatus;
    }
}
