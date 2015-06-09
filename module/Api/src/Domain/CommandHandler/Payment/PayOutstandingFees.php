<?php

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Payment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
// use Dvsa\Olcs\Api\Entity\Payment\Application;
use Dvsa\Olcs\Transfer\Command\Payment\PayOutstandingFees as Cmd;
use Dvsa\Olcs\Transfer\Query\Organisation\OutstandingFees as Qry;

/**
 * Pay Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayOutstandingFees extends AbstractCommandHandler
{
    protected $repoServiceName = 'Payment';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $organisationId = $command->getOrganisationId();
        if ($organisationId) {
            $query = Qry::create(['id' => $organisationId]);
            $response = $this->getQueryHandler()->handleQuery($query);
            $result = $response->getResult();
            exit;
            // fees
        } else {
            // not implemented
            // @todo throw Exception
        }
        /** @var Application $application */
        // $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // $application->setFinancialEvidenceUploaded($command->getFinancialEvidenceUploaded());

        // $this->getRepo()->save($application);

        // $result->addMessage('Financial evidence section has been updated');
        // return $result;
    }

    // old controller method
    protected function payFeesViaCpms($fees)
    {
        // Check for and resolve any outstanding payment requests
        $service = $this->getServiceLocator()->get('Cpms\FeePayment');
        $feesToPay = [];
        foreach ($fees as $fee) {
            if ($service->hasOutstandinogPayment($fee)) {
                $paid = $service->resolveOutstandingPayments($fee);
                if (!$paid) {
                    $feesToPay[] = $fee;
                }
            } else {
                $feesToPay[] = $fee;
            }
        }
        if (empty($feesToPay)) {
            // fees were all paid
            return $this->redirectToIndex();
        }

        $customerReference = $this->getCurrentOrganisationId();
        $redirectUrl = $this->getServiceLocator()->get('Helper\Url')
            ->fromRoute('fees/result', [], ['force_canonical' => true], true);

        try {
            $response = $service->initiateCardRequest($customerReference, $redirectUrl, $feesToPay);
        } catch (CpmsException\PaymentInvalidResponseException $e) {
            $this->addErrorMessage('payment-failed');
            return $this->redirectToIndex();
        }

        $view = new ViewModel(
            [
                'gateway' => $response['gateway_url'],
                'data' => [
                    'receipt_reference' => $response['receipt_reference']
                ]
            ]
        );
        $view->setTemplate('cpms/payment');

        return $this->render($view);
    }
}
