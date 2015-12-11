<?php

namespace Dvsa\Olcs\Api\Entity\Fee;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\ContactDetails\Address;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
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
    const METHOD_REFUND       = 'fpm_refund';
    const METHOD_REVERSAL     = 'fpm_reversal';

    const DEFAULT_INVOICE_CUSTOMER_NAME = 'Miscellaneous payment';
    const DEFAULT_INVOICE_ADDRESS_LINE = 'Miscellaneous payment';
    // CPMS enforces 'valid' postcodes :(
    const DEFAULT_POSTCODE = 'LS9 6NF';

    public function __construct(FeeType $feeType, $netAmount, RefData $feeStatus)
    {
        parent::__construct();

        $this->feeType = $feeType;
        $this->netAmount = $netAmount;
        $this->feeStatus = $feeStatus;

        $this->setVatAndGrossAmountsFromNetAmountUsingRate($feeType->getVatRate());
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

    public function getDefermentPeriod()
    {
        $map = [
            self::ACCRUAL_RULE_LICENCE_START => 60,
            self::ACCRUAL_RULE_CONTINUATION  => 60,
            self::ACCRUAL_RULE_IMMEDIATE     => 1,
        ];

        $rule = $this->getFeeType()->getAccrualRule()->getId();

        if (array_key_exists($rule, $map)) {
            return $map[$rule];
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
        $transaction = $this->getOutstandingWaiveTransaction();
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

        if (empty($feeTransactions) || $feeTransactions->isEmpty()) {
            return;
        }

        $ft = $feeTransactions->filter(
            function ($ft) {
                $transaction = $ft->getTransaction();
                return (
                    $transaction->getType()->getId() === Transaction::TYPE_WAIVE
                    &&
                    $transaction->isOutstanding()
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
        $amount = self::amountToPence($this->getGrossAmount());

        $ftSum = 0;
        $this->getFeeTransactions()->forAll(
            function ($key, $feeTransaction) use (&$ftSum) {
                unset($key); // unused
                if ($feeTransaction->getTransaction()->isComplete()) {
                    $ftSum += self::amountToPence($feeTransaction->getAmount());
                }
                return true;
            }
        );

        return self::amountToPounds($amount - $ftSum);
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

            if (!empty($transactions)) {
                $key = array_keys($transactions)[0];
                return $this->getFeeTransactions()->get($key);
            }
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
            'amount' => $this->getGrossAmount(),
        ];
    }

    /**
     * @return Organisation|null
     */
    public function getOrganisation()
    {
        if (!empty($this->getLicence())) {
            return $this->getLicence()->getOrganisation();
        }

        if (!empty($this->getIrfoGvPermit())) {
            return $this->getIrfoGvPermit()->getOrganisation();
        }
    }

    /**
     * @return string
     */
    public function getCustomerNameForInvoice()
    {
        $name = self::DEFAULT_INVOICE_CUSTOMER_NAME;

        if (!empty($this->getOrganisation())) {
            $name = $this->getOrganisation()->getName();
        }

        return $name;
    }

    /**
     * @return Address
     */
    public function getCustomerAddressForInvoice()
    {
        if (!empty($this->getLicence())) {
            $contactDetails = $this->getLicence()->getCorrespondenceCd();
            if (!empty($contactDetails)) {
                return $contactDetails->getAddress();
            }
        }

        if (!empty($this->getIrfoGvPermit())) {
            $organisation = $this->getIrfoGvPermit()->getOrganisation();
            if (!empty($organisation)) {
                $contactDetails = $organisation->getIrfoContactDetails();
                if (!empty($contactDetails)) {
                    return $contactDetails->getAddress();
                }
            }
        }

        // we always need to return a valid address object
        $default = new Address();
        $default
            ->setAddressLine1(self::DEFAULT_INVOICE_ADDRESS_LINE)
            ->setTown(self::DEFAULT_INVOICE_ADDRESS_LINE)
            ->setPostcode(self::DEFAULT_POSTCODE);
        return $default;
    }

    /**
     * @return boolean
     */
    public function isBalancingFee()
    {
        return $this->getFeeType()->isAdjustment();
    }

    /**
     * @return boolean
     */
    public function isNewApplicationFee()
    {
        return $this->getFeeType()->getFeeType()->getId() === FeeType::FEE_TYPE_APP;
    }

    /**
     * @return boolean
     */
    public function isVariationFee()
    {
        return $this->getFeeType()->getFeeType()->getId() === FeeType::FEE_TYPE_VAR;
    }

    /**
     * @return boolean
     */
    public function isGrantFee()
    {
        return $this->getFeeType()->getFeeType()->getId() === FeeType::FEE_TYPE_GRANT;
    }

    /**
     * Get the 'sales person reference', also known as 'cost centre reference'
     * for a fee. This is either a traffic area code or one of:
     *
     * @return string
     */
    public function getSalesPersonReference()
    {
        $costCentreRef = $this->getFeeType()->getCostCentreRef();

        if ($costCentreRef === FeeType::COST_CENTRE_REF_TYPE_LICENSING) {
            return $this->getLicence()->getTrafficArea()->getSalesPersonReference();
        }

        return $costCentreRef;
    }

    /**
     * @return bool
     */
    public function isPartPaid()
    {
        return $this->getOutstandingAmount() < $this->getGrossAmount();
    }

    /**
     * @return bool
     */
    public function isFullyOutstanding()
    {
        return $this->getFeeStatus()->getId() === self::STATUS_OUTSTANDING && !$this->isPartPaid();
    }

    /**
     * @return bool
     */
    public function isOutstanding()
    {
        return $this->getFeeStatus()->getId() === self::STATUS_OUTSTANDING;
    }

    /**
     * @return boolean
     */
    public function isCancelled()
    {
        return $this->getFeeStatus()->getId() === self::STATUS_CANCELLED;

    }

    public function isPaid()
    {
        return $this->getFeeStatus()->getId() === self::STATUS_PAID;
    }

    /**
     * @return bool
     */
    public function canRefund()
    {

        if ($this->getFeeType()->isMiscellaneous()) {
            // miscellaneous fees are not currently refundable
            return false;
        }

        if ($this->isCancelled()) {
            // cancelled fees are not refundable
            return false;
        }

        // can only refund if there are non-refunded payments
        foreach ($this->getFeeTransactions() as $ft) {
            if ($ft->getTransaction()->isPayment() && !$ft->isRefundedOrReversed()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getFeeTransactionsForRefund()
    {
        $feeTransactions = [];

        foreach ($this->getFeeTransactions() as $ft) {
            $txn = $ft->getTransaction();
            if ($txn->isPayment() && $txn->isComplete() && !$ft->isRefundedOrReversed()) {
                $feeTransactions[] = $ft;
            }
        }

        return $feeTransactions;
    }

    /**
     * Method to encapsulate the VAT calculations. Takes a percentage rate
     * parameter and expects net amount to be already set.
     *
     * @param float $rate percentage e.g. 20 or 17.5
     */
    public function setVatAndGrossAmountsFromNetAmountUsingRate($rate)
    {
        $net = $this->getNetAmount();

        $vat = $net * $rate; // this gives value in pence
        $vat = floor($vat); // round down to nearest penny
        $vat = self::amountToPounds($vat); // convert to pounds

        $this->setVatAmount($vat);

        $this->setGrossAmount($net + $vat);
    }

    /**
     * @param string $amount e.g. '4.56'
     * @return int e.g. 456
     */
    public static function amountToPence($amount)
    {
        return (int) number_format($amount, 2, '', '');
    }

    /**
     * @param int $amount e.g. 456
     * @return string e.g. '4.56'
     */
    public static function amountToPounds($amount)
    {
        return number_format($amount / 100, 2, '.', '');
    }

    /**
     * @return string formatted amount
     */
    public function getAmountAllocatedByTransactionId($transactionId)
    {
        $amount = null;

        $this->getFeeTransactions()->forAll(
            function ($key, $feeTransaction) use ($transactionId, &$amount) {
                unset($key); // unused
                if ($feeTransaction->getTransaction()->getId() == $transactionId) {
                    $amount = $feeTransaction->getAmount();
                    return false;
                }
                return true;
            }
        );

        return $amount;
    }

    /**
     * Adjust a transaction amount. We wouldn't normally persist this change
     * (we would create negative fee transactions and then add new positive ones)
     * but this is useful for calculating the adjusted amounts we need to pass to
     * CPMS
     */
    public function adjustTransactionAmount($transactionId, $adjustment)
    {
        $this->getFeeTransactions()->forAll(
            function ($key, $feeTransaction) use ($transactionId, &$adjustment) {
                unset($key); // unused
                if ($feeTransaction->getTransaction()->getId() == $transactionId) {
                    $amount = $feeTransaction->getAmount();
                    $feeTransaction->setAmount($amount + $adjustment);
                    return false;
                }
                return true;
            }
        );
    }
}
