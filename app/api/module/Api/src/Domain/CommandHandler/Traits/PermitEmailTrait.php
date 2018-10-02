<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;

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
            $vars['permitsGranted'] = $irhpPermitApplication->countValidPermits();
            $vars['paymentDeadlineNumDays'] = '10';

            $criteria = Criteria::create();
            $criteria->where(
                $criteria->expr()->in(
                    'fee_status',
                    [
                        FeeEntity::STATUS_OUTSTANDING,
                    ]
                )
            );

            $fees = $recordObject->getFees()->matching($criteria);
            $feeTypesAmounts = [];

            foreach ($fees as $fee)
            {
                if ($fee->isEcmtIssuingFee()) {
                    $feeTypesAmounts[] = [
                        'issueFeeAmount' => $fee->getFeeTypeAmount(),
                        'issueFeeTotal' => $fee->getOutstandingAmount(),
                        'invoicedDate' => $fee->getInvoicedDateTime()
                    ];
                }

            }

            if (count($feeTypesAmounts) !== 1) {
                throw new Exception('There should be exactly one issuing fee.');
            }

            $vars['issueFeeDeadlineDate'] = $this->calculateDueDate($feeTypesAmounts[0]['invoicedDate']);

            $vars['issueFeeAmount'] = $feeTypesAmounts[0]['issueFeeAmount'];
            $vars['issueFeeTotal'] = $feeTypesAmounts[0]['issueFeeTotal'];
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

    /**
     * Calculate due date for payment
     *
     * @param string $date
     *
     * @return string
     */
    protected function calculateDueDate($date)
    {
        return date(
            \DATE_FORMAT,
            strtotime(
                "+10 days",
                strtotime(
                    $date
                )
            )
        );
    }
}
