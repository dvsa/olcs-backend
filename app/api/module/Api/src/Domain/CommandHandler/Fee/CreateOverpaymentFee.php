<?php

/**
 * Create Overpayment Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as CreateFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create Overpayment Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateOverpaymentFee extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'FeeType';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
        return $this;
    }

    /**
     * Given a payment amount and an array of fees, will create an overpayment
     * balancing fee if required.
     */
    public function handleCommand(CommandInterface $command)
    {
        $fees = $command->getFees();
        $receivedAmount =  $command->getReceivedAmount();

        if ($command->getPreviousBalancingFee()) {
            $this->cancelPreviousOverpayment($command->getPreviousBalancingFee(), &$fees);
        }

        $overpaymentAmount = $this->feesHelper->getOverpaymentAmount($receivedAmount, $fees);

        if ($overpaymentAmount > 0) {

            // sort fees
            $fees = $this->feesHelper->sortFeesByInvoiceDate($fees);

            // get IDs for description
            $feeIds = array_map(
                function ($fee) {
                    return $fee->getId();
                },
                $fees
            );

            // we get licenceId, applicationId, busRegId, irfoGvPermitId from
            // the first existing fee
            $existingFee = reset($fees);
            $ids = $this->feesHelper->getIdsFromFee($existingFee);

            // get correct feeType
            $feeType = $this->getRepo()->fetchLatestForOverpayment();

            $dtoData = array_merge(
                [
                    'amount'       => $overpaymentAmount,
                    'invoicedDate' => (new DateTime())->format(\DateTime::W3C),
                    'feeType'      => $feeType->getId(),
                    'description'  => 'Overpayment on fees: ' . implode(', ', $feeIds),
                ],
                $ids
            );

            $this->result->merge($this->handleSideEffect(CreateFeeCmd::create($dtoData)));
        }

        return $this->result;
    }

    /**
     * Cancel an existing overpayment fee and remove it from the passed-in
     * array of fees
     *
     * @param FeeEntity $fee
     * @param array $fees passed by reference, may be modified
     * @return null
     */
    private function cancelPreviousOverpayment($fee, &$fees) {

        $this->result->merge(
            $this->handleSideEffect(CancelFeeCmd::create(['id' => $fee->getId()]))
        );

        unset($fees[$fee->getId()]);
    }
}
