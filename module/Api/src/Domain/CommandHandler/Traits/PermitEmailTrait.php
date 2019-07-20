<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

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
     * Get subject variables
     *
     * @param mixed $recordObject
     *
     * @return array
     */
    protected function getSubjectVariables($recordObject): array
    {
        return [
            'applicationRef' => $recordObject->getApplicationRef(),
        ];
    }

    /**
     * Calculate due date for payment
     *
     * @param string $date
     *
     * @return string
     */
    protected function calculateDueDate(\DateTime $date)
    {
        // TODO - OLCS-21979
        $date->add(\DateInterval::createFromDateString('+9 weekdays'));
        return $date->format('d F Y');
    }
}
