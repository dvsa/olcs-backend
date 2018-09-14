<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;

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
    public function getRecipients($recordObject): array
    {
        return $this->organisationRecipients(
            $recordObject->getLicence()->getOrganisation(),
            $recordObject->getCreatedBy()
        );
    }

    /**
     * Get template variables
     *
     * @param EcmtPermitApplication $application
     *
     * @return array
     */
    protected function getTemplateVariables(EcmtPermitApplication $application): array
    {
        return [
            // http://selfserve is replaced based on the environment
            'appUrl' => 'http://selfserve',
            'permitsUrl' => 'http://selfserve/permits',
            'guidanceUrl' => 'https://www.gov.uk/guidance/international-authorisations-and-permits-for-road-haulage',
            'applicationRef' => $application->getApplicationRef(),
        ];
    }

    /**
     * Get subject variables
     *
     * @param EcmtPermitApplication $application
     *
     * @return array
     */
    protected function getSubjectVariables(EcmtPermitApplication $application): array
    {
        return [
            'applicationRef' => $application->getApplicationRef(),
        ];
    }
}
