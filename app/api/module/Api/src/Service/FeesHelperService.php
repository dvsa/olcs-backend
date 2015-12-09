<?php

/**
 * Fees Helper Service
 *
 * NOTE: Does calculations as integers/pence wherever possible in order to avoid
 * floating point rounding errors.
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fees Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeesHelperService implements FactoryInterface
{
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Application
     */
    protected $applicationRepo;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Fee
     */
    protected $feeRepo;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\FeeType
     */
    protected $feeTypeRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoManager = $serviceLocator->get('RepositoryServiceManager');

        // inject required repos
        $this->applicationRepo = $repoManager->get('Application');
        $this->feeRepo = $repoManager->get('Fee');
        $this->feeTypeRepo = $repoManager->get('FeeType');

        return $this;
    }

    /**
     * Get fees pertaining to an application
     *
     * AC specify we should only get the *latest* application and interim
     * fees in the event there are multiple fees outstanding.
     *
     * @param int $applicationId
     * @return array
     */
    public function getOutstandingFeesForApplication($applicationId)
    {
        $outstandingFees = [];

        $application = $this->applicationRepo->fetchById($applicationId);

        // get application fee
        $applicationFee = $application->getLatestOutstandingApplicationFee();
        if (!empty($applicationFee)) {
            $outstandingFees[] = $applicationFee;
        }

        // get interim fee if applicable
        if ($application->isGoods()) {
            $interimFee = $application->getLatestOutstandingInterimFee();
            if (!empty($interimFee)) {
                $outstandingFees[] = $interimFee;
            }
        }

        return $outstandingFees;
    }

    /**
     * Gets the minimum allowable payment amount for an array of fees,
     * this prevents creating invalid payment attempts which would result
     * in a zero allocated amount
     *
     * @param array $fees
     * @return string formatted amount
     */
    public function getMinPaymentForFees(array $fees)
    {
        $fees = $this->sortFeesByInvoiceDate($fees);

        // min. payment must be greater than the outstanding amount for all but the last fee
        $minPayment = 1; // 1 penny
        for ($i=0; $i < count($fees) -1; $i++) {
            $minPayment += FeeEntity::amountToPence($fees[$i]->getOutstandingAmount());
        }

        return FeeEntity::amountToPounds($minPayment);
    }

    /**
     * Gets the total outstanding payment amount for an array of fees
     *
     * @param array $fees
     * @return string formatted amount
     */
    public function getTotalOutstanding(array $fees)
    {
        $total = 0;

        foreach ($fees as $fee) {
            $total += FeeEntity::amountToPence($fee->getOutstandingAmount());
        }

        return FeeEntity::amountToPounds($total);
    }

    /**
     * Determine how a payment should be allocated to an array of fees.
     * Payment is allocated to earliest fees first (by invoicedDate)
     *
     * Zero allocations are not returned
     *
     * @param string $amount payment amount
     * @param array $fees array of FeeEntity
     * @return array ['feeId' => 'allocatedAmount'] e.g.
     * [
     *     97 => '12.34',
     *     98 => '50.00',
     * ]
     */
    public function allocatePayments($amount, array $fees)
    {
        $fees = $this->sortFeesByInvoiceDate($fees);

        $allocations = [];

        $remaining = FeeEntity::amountToPence($amount);

        foreach ($fees as $fee) {

            if ($fee->isCancelled()) {
                continue;
            }

            $allocated = 0;
            $outstanding = FeeEntity::amountToPence($fee->getOutstandingAmount());

            if ($remaining >= $outstanding) {
                // if we have enough to pay the fee in full, allocate full amount
                $allocated = $outstanding;
            } elseif ($remaining > 0) {
                // otherwise allocate remaining available amount
                $allocated = $remaining;
            }

            // then decrement remaining available
            $remaining = ($remaining - $allocated);

            if ($allocated > 0) {
                $allocations[$fee->getId()] = FeeEntity::amountToPounds($allocated);
            }
        }

        if ($remaining > 0) {
            // note, a balancing fee for any overpayment should always be created
            // prior to calculating allocations, so keep this in as a safeguard:
            throw new Exception("Overpayments not permitted");
        }

        return $allocations;
    }

    /**
     * Determine the amounts by which to adjust fees by reversing a previous
     * transaction
     *
     * @param string $transactionId
     * @param array $fees array of FeeEntity
     * @return array ['feeId' => 'allocatedAmount'] e.g.
     * [
     *     97 => '-12.34',
     *     98 => '-50.00',
     * ]
     */
    public function deallocatePayments($transactionId, array $fees)
    {
        $allocations = [];

        foreach ($fees as $fee) {
            $allocated = $fee->getAmountAllocatedByTransactionId($transactionId);
            $allocations[$fee->getId()] = number_format($allocated * -1, 2, '.', '');
        }

        return $allocations;
    }

    public function reallocatePayments($newAmount, array $fees, $originalTransactionId)
    {
        $deallocations = $this->deallocatePayments($originalTransactionId, $fees);

        foreach ($fees as &$fee) {
            $fee->adjustTransactionAmount($originalTransactionId, $deallocations[$fee->getId()]);
        }

        return $this->allocatePayments($newAmount, $fees);
    }

    public function sortFeesByInvoiceDate(array $fees)
    {
        $sorted = $fees;
        // sort fees in invoicedDate order
        usort(
            $sorted,
            function ($a, $b) {
                if ($a->getInvoicedDate() === $b->getInvoicedDate()) {
                    // if invoicedDate the same, use id as a tie-break
                    return $a->getId() < $b->getId() ? -1 : 1;
                }
                return $a->getInvoicedDate() < $b->getInvoicedDate() ? -1 : 1;
            }
        );

        return $sorted;
    }

    /**
     * Calculate amount of any overpayment. Note, will return a negative value
     * for an underpayment, although not really expected to be used as such
     *
     * @param string $amount payment amount
     * @param array $fees array of FeeEntity
     * @return string formatted amount
     */
    public function getOverpaymentAmount($receivedAmount, $fees)
    {
        $receivedAmount = FeeEntity::amountToPence($receivedAmount);
        $outstanding = FeeEntity::amountToPence($this->getTotalOutstanding($fees));

        $overpayment = $receivedAmount - $outstanding;

        return FeeEntity::amountToPounds($overpayment);
    }

    /**
     * @param FeeEntity $existingFee
     * @return array
     */
    public function getIdsFromFee($existingFee)
    {
        $licenceId = null;
        if ($existingFee->getLicence()) {
            $licenceId = $existingFee->getLicence()->getId();
        }

        $applicationId = null;
        if ($existingFee->getApplication()) {
            $applicationId = $existingFee->getApplication()->getId();
        }

        $busRegId = null;
        if ($existingFee->getBusReg()) {
            $busRegId = $existingFee->getBusReg()->getId();
        }

        $irfoGvPermitId = null;
        if ($existingFee->getIrfoGvPermit()) {
            $irfoGvPermitId = $existingFee->getIrfoGvPermit()->getId();
        }

        return [
            'licence'      => $licenceId,
            'application'  => $applicationId,
            'busReg'       => $busRegId,
            'irfoGvPermit' => $irfoGvPermitId,
        ];
    }
}
