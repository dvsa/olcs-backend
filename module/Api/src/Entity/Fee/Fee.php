<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Doctrine\Common\Collections\Criteria;

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
 *        @ORM\Index(name="ix_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_fee_irfo_gv_permit_id", columns={"irfo_gv_permit_id"}),
 *        @ORM\Index(name="ix_fee_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_fee_fee_status", columns={"fee_status"})
 *    }
 * )
 */
class Fee extends AbstractFee
{
    const STATUS_OUTSTANDING       = 'lfs_ot';
    const STATUS_PAID              = 'lfs_pd';
    const STATUS_WAIVE_RECOMMENDED = 'lfs_wr'; // @deprecated
    const STATUS_WAIVED            = 'lfs_w'; // @deprecated
    const STATUS_CANCELLED         = 'lfs_cn';

    const ACCRUAL_RULE_LICENCE_START = 'acr_licence_start';
    const ACCRUAL_RULE_CONTINUATION  = 'acr_continuation';
    const ACCRUAL_RULE_IMMEDIATE     = 'acr_immediate';

    const METHOD_CARD_ONLINE  = 'fpm_card_online';
    const METHOD_CARD_OFFLINE = 'fpm_card_offline';
    const METHOD_CASH         = 'fpm_cash';
    const METHOD_CHEQUE       = 'fpm_cheque';
    const METHOD_POSTAL_ORDER = 'fpm_po';
    const METHOD_WAIVE        = 'fpm_waive';

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
        foreach ($this->getFeeTransactions() as $fp) {
            if ($fp->getTransaction()->isOutstanding()) {
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
     * @return DateTime|null
     */
    public function getRuleStartDate()
    {
        $rule = $this->getFeeType()->getAccrualRule()->getId();
        switch ($rule) {
            case self::ACCRUAL_RULE_IMMEDIATE:
                return new DateTime();
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

    public function allowEdit()
    {
        return !in_array(
            $this->getFeeStatus()->getId(),
            [
                self::STATUS_PAID,
                self::STATUS_CANCELLED,
            ]
        );
    }

    /**
     * @deprecated will be removed in OLCS-10426
     * @see getLatestPaymentRef()
     */
    public function getReceiptNo()
    {
        return $this->getLatestPaymentRef();
    }

    /**
     * @todo OLCS-10407 this currently assumes only one transaction against a
     * fee, will need updating when part payments are allowed
     */
    public function getReceivedAmount()
    {
        $ft = $this->getLatestFeeTransaction();
        if ($ft) {
            return $ft->getAmount();
        }
    }

    /**
     * @todo OLCS-10425 will remove the need for this method
     */
    public function getReceivedDate()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getCompletedDate();
        }
    }

    public function getPaymentMethod()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getPaymentMethod();
        }
    }

    public function getProcessedBy()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            $user = $transaction->getProcessedByUser();
            if ($user) {
                return $user->getLoginId();
            }
        }
    }

    public function getPayer()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getPayerName();
        }
    }

    public function getSlipNo()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getPayingInSlipNumber();
        }
    }

    public function getChequePoNumber()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getChequePoNumber();
        }
    }

    public function getWaiveReason()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getComment();
        }
    }

    /**
     * @return Transaction
     */
    public function getOutstandingWaiveTransaction()
    {
        $feeTransactions = $this->getFeeTransactions();

        if (empty($feeTransactions)) {
            return;
        }

        $ft = $feeTransactions->filter(
            function ($ft) {
                $transaction = $ft->getTransaction();
                return (
                    $transaction->getType()->getId() === Transaction::TYPE_WAIVE
                    &&
                    $transaction->getStatus()->getId() === Transaction::STATUS_OUTSTANDING
                );
            }
        )->first(); // there should only ever be one!

        return $ft ? $ft->getTransaction() : null;
    }

    /**
     * @return string e.g. '1234.56'
     */
    public function getOutstandingAmount()
    {
        $amount = (float) $this->getAmount();

        $ftSum = 0;
        $feeTransactions = $this->getFeeTransactions()->forAll(
            function ($key, $feeTransaction) use (&$ftSum) {
                if ($feeTransaction->getTransaction()->isComplete()) {
                    $ftSum += (float) $feeTransaction->getAmount();
                    return true;
                }
            }
        );

        return number_format(($amount - $ftSum), 2, '.', '');
    }

    /**
     * @return string|null
     */
    public function getLatestPaymentRef()
    {
        $transaction = $this->getLatestTransaction();
        if ($transaction) {
            return $transaction->getReference();
        }
    }

    /**
     * @return FeeTransaction
     */
    protected function getLatestFeeTransaction()
    {
        // Criteria won't handle relations, only properties, so get them all and
        // sort the transactions manually
        if ($this->getFeeTransactions() && $this->getFeeTransactions()->count()) {
            $transactions = [];
            foreach ($this->getFeeTransactions() as $key => $ft) {
                if ($ft->getTransaction()->isComplete()) {
                    $transactions[$key] = $ft->getTransaction();
                }
            }
            uasort(
                $transactions,
                function ($a, $b) {
                    if ($a->getCompletedDate() == $b->getCompletedDate()) {
                        // if same date, use createdOn timestamp
                        return $a->getCreatedOn() < $b->getCreatedOn();
                    }
                    return $a->getCompletedDate() < $b->getCompletedDate();
                }
            );
            $key = array_keys($transactions)[0];
            return $this->getFeeTransactions()->get($key);
        }
    }

    /**
     * @return Transaction
     */
    protected function getLatestTransaction()
    {
        $ft = $this->getLatestFeeTransaction();
        if ($ft) {
            return $ft->getTransaction();
        }
    }

    /**
     * @return array
     */
    public function getCalculatedBundleValues()
    {
        return [
            'outstanding' => $this->getOutstandingAmount(),
            'receiptNo' => $this->getLatestPaymentRef(),
        ];
    }
}
