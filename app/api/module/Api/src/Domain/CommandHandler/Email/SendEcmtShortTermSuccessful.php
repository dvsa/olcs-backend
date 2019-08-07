<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\CommandHandler\Traits\PermitEmailTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Send confirmation of ECMT short term app being successful
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SendEcmtShortTermSuccessful extends AbstractEmailHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use PermitEmailTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';
    protected $template = 'ecmt-short-term-app-successful';
    protected $subject = 'email.ecmt.short.term.response.subject';

    /**
     * Get template variables
     *
     * @param IrhpApplication $recordObject
     *
     * @return array
     */
    protected function getTemplateVariables($recordObject): array
    {
        $this->getRepo()->refresh($recordObject);

        $irhpPermitApplication = $recordObject->getFirstIrhpPermitApplication();
        $issueFee = $recordObject->getLatestOutstandingIssueFee();
        $invoicedDateTime = $issueFee->getInvoicedDateTime();
        $irhpApplicationId = $recordObject->getId();

        return [
            'applicationRef' => $recordObject->getApplicationRef(),
            'euro5PermitsGranted' => $irhpPermitApplication->getRequiredEuro5(),
            'euro6PermitsGranted' => $irhpPermitApplication->getRequiredEuro6(),
            'issueFeeAmount' => $this->formatCurrency($issueFee->getFeeTypeAmount()),
            'issueFeeTotal' => $this->formatCurrency($issueFee->getOutstandingAmount()),
            'paymentDeadlineNumDays' => '10', // TODO - OLCS-21979
            'issueFeeDeadlineDate' => $this->calculateDueDate($invoicedDateTime),
            'paymentUrl' => 'http://selfserve/permits/application/' . $irhpApplicationId . '/awaiting-fee',
        ];
    }

    /**
     * Format a fee as currency
     *
     * param float $amount
     *
     * @return array
     */
    private function formatCurrency($amount)
    {
         return str_replace('.00', '', $amount);
    }
}
