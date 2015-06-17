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

    const ACCRUAL_RULE_LICENCE_START = 'acr_licence_start';
    const ACCRUAL_RULE_CONTINUATION = 'acr_continuation';
    const ACCRUAL_RULE_IMMEDIATE = 'acr_immediate';

    const METHOD_CARD_ONLINE = 'fpm_card_online';
    const METHOD_CARD_OFFLINE = 'fpm_card_offline';

    public function __construct(FeeType $feeType, $amount, RefData $feeStatus)
    {
        parent::__construct();

        $this->feeType = $feeType;
        $this->amount = $amount;
        $this->feeStatus = $feeStatus;
    }

    /**
     * Loop through a fee's payment records and check if any are outstanding
     */
    public function hasOutstandingPayment()
    {
        foreach ($this->getFeePayments() as $fp) {
            if ($fp->getPayment()->isOutstanding()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine 'rule start date' for a fee
     *
     * @see https://jira.i-env.net/browse/OLCS-6005 for business rules
     *
     * @return \DateTime|null
     */
    public function getRuleStartDate()
    {
        $rule = $this->getFeeType()->getAccrualRule()->getId();
        switch ($rule) {
            case self::ACCRUAL_RULE_IMMEDIATE:
                return $this->getCurrentDateTime();
            case self::ACCRUAL_RULE_LICENCE_START:
                $licenceStart = $this->getLicence()->getInForceDate();
                if (!is_null($licenceStart)) {
                    return new \DateTime($licenceStart);
                }
                break;
            case self::ACCRUAL_RULE_CONTINUATION:
                // The licence continuation date + 1 day (according to calendar dates)
                $licenceExpiry = $this->getLicence()->getExpiryDate();
                if (!is_null($licenceExpiry)) {
                    $date = new \DateTime($licenceExpiry);
                    $date->add(new \DateInterval('P1D'));
                    return $date;
                }
                break;
            default:
                break;
        }
    }

    /**************************************************************************/
    /* Allow injection of current date/time */

    /**
     * @var \DateTime $now
     * @todo migrate this to use Util\DateTime class when available
     */
    protected $now;

    /**
     * @return \DateTime
     */
    public function getCurrentDateTime()
    {
        if (is_null($this->now)) {
            $this->now = new \DateTime();
        }
        return $this->now;
    }

    /**
     * @param \DateTime $datetime
     * @return $this
     */
    public function setCurrentDateTime(\DateTime $datetime)
    {
        $this->now = $datetime;
    }
    /**************************************************************************/
}
