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
        $applicationFee = $this->getLatestOutstandingApplicationFeeForApplication($application);
        if (!empty($applicationFee)) {
            $outstandingFees[] = $applicationFee;
        }

        // get interim fee if applicable
        if ($application->isGoods()) {
            $interimFee = $this->getLatestOutstandingInterimFeeForApplication($application);
            if (!empty($interimFee)) {
                $outstandingFees[] = $interimFee;
            }
        }

        return $outstandingFees;
    }

    /**
     * @param ApplicationEntity $application
     * @return FeeEntity
     */
    protected function getLatestOutstandingApplicationFeeForApplication(ApplicationEntity $application)
    {
        if ($application->isVariation()) {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_VAR;
        } else {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_APP;
        }

        return $this->getLatestOutstandingFeeForApplicationByType($application, $feeTypeFeeTypeId);
    }

    /**
     * @param ApplicationEntity $application
     * @return FeeEntity
     */
    protected function getLatestOutstandingInterimFeeForApplication(ApplicationEntity $application)
    {
        return $this->getLatestOutstandingFeeForApplicationByType($application, FeeTypeEntity::FEE_TYPE_GRANTINT);
    }

    /**
     * @param ApplicationEntity $application
     * @param string $feeTypeFeeTypeId
     * @return FeeEntity
     */
    protected function getLatestOutstandingFeeForApplicationByType($application, $feeTypeFeeTypeId)
    {
        if (is_null($application->getLicenceType())) {
            return;
        }

        $applicationDate = new \DateTime($application->getApplicationDate());

        $feeType = $this->feeTypeRepo->fetchLatest(
            $this->feeTypeRepo->getRefdataReference($feeTypeFeeTypeId),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $applicationDate,
            $application->getFeeTrafficAreaId()
        );

        return $this->feeRepo->fetchLatestFeeByTypeStatusesAndApplicationId(
            $feeType->getId(),
            [FeeEntity::STATUS_OUTSTANDING],
            $application->getId()
        );
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
        $minPayment = 0.01;
        for ($i=0; $i < count($fees) -1; $i++) {
            $minPayment += $fees[$i]->getOutstandingAmount();
        }

        return $this->format($minPayment);
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
            $total += $fee->getOutstandingAmount();
        }

        return $this->format($total);
    }

    /**
     * Determine how a payment should be allocated to an array of fees.
     * Payment is allocated to earliest fees first (by invoicedDate)
     *
     * @param string $amount payment amount
     * @param array $fees array of FeeEntity
     * @return array ['feeId' => 'allocatedAmount'] e.g.
     * [
     *     97 => '12.34',
     *     98 => '50.00',
     *     99 => '0.00',
     * ]
     */
    public function allocatePayments($amount, array $fees)
    {
        $fees = $this->sortFeesByInvoiceDate($fees);

        $allocations = [];

        $remaining = (int) ($amount * 100);

        foreach ($fees as $fee) {

            $allocated = 0;
            $outstanding = (int) ($fee->getOutstandingAmount() * 100);

            if ($remaining >= $outstanding) {
                // if we have enough to pay the fee in full, allocate full amount
                $allocated = $outstanding;
            } elseif ($remaining > 0) {
                // otherwise allocate remaining available amount
                $allocated = $remaining;
            }

            // then decrement remaining available
            $remaining = ($remaining - $allocated);

            $allocations[$fee->getId()] = $this->format($allocated / 100);
        }

        if ($remaining > 0) {
            // note, a balancing fee for any overpayment should always be created
            // prior to calculating allocations, so keep this in as a safeguard:
            throw new Exception("Overpayments not permitted");
        }

        return $allocations;
    }

    public function sortFeesByInvoiceDate(array $fees)
    {
        $sorted = $fees;
        // sort fees in invoicedDate order
        uasort(
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
        $receivedAmount = (int) ($receivedAmount * 100);
        $outstanding = (int) ($this->getTotalOutstanding($fees) * 100);

        $overpayment = $receivedAmount - $outstanding;

        return $this->format($overpayment / 100);
    }

    /**
     * @param float $amount
     * @return string formatted amount - two decimal places, no thousands separator
     */
    private function format($amount) {
        return number_format($amount, 2, '.', '');
    }
}
