<?php

/**
 * Create Overpayment Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee as CreateFeeCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Fee\Transaction as TransactionEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
        return parent::createService($serviceLocator);
    }

    /**
     * Given a payment amount and an array of fees, will create an overpayment
     * balancing fee if required.
     */
    public function handleCommand(CommandInterface $command)
    {
        $fees = $command->getFees();
        $receivedAmount =  $command->getReceivedAmount();

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
}
