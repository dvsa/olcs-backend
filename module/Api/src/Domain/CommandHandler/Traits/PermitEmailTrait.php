<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;

/**
 * Permit email trait
 */
trait PermitEmailTrait
{
    /**
     * Returns array of addresses based on the permit application
     * NOTE makes use of EmailAwareTrait, try to think of a better way of arranging this
     *
     * @param object $recordObject
     *
     * @return array
     */
    protected function getRecipients($recordObject): array
    {
        return $this->organisationRecipients(
            $recordObject->getLicence()->getOrganisation(),
            $recordObject->getCreatedBy()
        );
    }

    /**
     * Get template variables
     *
     * @param EcmtPermitApplication $recordObject
     *
     * @return array
     */
    protected function getTemplateVariables($recordObject): array
    {
        $vars = [
            // http://selfserve is replaced based on the environment
            'appUrl' => 'http://selfserve/',
            'permitsUrl' => 'http://selfserve/permits',
            'guidanceUrl' => 'https://www.gov.uk/guidance/international-authorisations-and-permits-for-road-haulage',
            'applicationRef' => $recordObject->getApplicationRef(),
        ];

        if ($recordObject->isValid()) {
            /** @var IrhpPermitApplication $irhpPermitApplication */
            $irhpPermitApplication = $recordObject->getIrhpPermitApplications()->first();

            $vars['permitsRequired'] = $recordObject->getPermitsRequired();
            $vars['permitsGranted'] = 10; //testing only
            $vars['paymentDeadlineNumDays'] = $irhpPermitApplication->getIrhpPermitWindow()->getDaysForPayment();
            $vars['issueFeeDeadlineDate'] = '13th of December 2018';
            $vars['issueFeeAmount'] = 123; //testing only - needs to come from DB
            $vars['issueFeeTotal'] = 1230; //testing only - needs to come from DB
        }
    }

    /**
     * Get subject variables
     *
     * @param EcmtPermitApplication $recordObject
     *
     * @return array
     */
    protected function getSubjectVariables($recordObject): array
    {
        return [
            'applicationRef' => $recordObject->getApplicationRef(),
        ];
    }
}
