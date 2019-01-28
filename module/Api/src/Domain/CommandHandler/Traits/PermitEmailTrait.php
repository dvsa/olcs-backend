<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

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
            'applicationFee' => str_replace(
                '.00',
                '',
                $this->getRepo('FeeType')->getLatestByProductReference(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF)->getAmount()
            ),
        ];

        if ($recordObject->isAwaitingFee()) {
            /** @var IrhpPermitApplication $irhpPermitApplication */
            $irhpPermitApplication = $recordObject->getIrhpPermitApplications()->first();

            $vars['awaitingFeeUrl'] = 'http://selfserve/permits/' . (int)$recordObject->getId() . '/ecmt-awaiting-fee/';
            $vars['permitsRequired'] = $recordObject->getPermitsRequired();
            $vars['permitsGranted'] = $irhpPermitApplication->countPermitsAwarded();
            $vars['paymentDeadlineNumDays'] = '10';

            $criteria = Criteria::create();
            $criteria->where(
                $criteria->expr()->eq(
                    'feeStatus',
                    $this->getRepo()->getRefdataReference(FeeEntity::STATUS_OUTSTANDING)
                )
            );

            $fees = $recordObject->getFees()->matching($criteria);
            $feeTypesAmounts = [];

            /** @var Fee $fee */
            foreach ($fees as $fee) {
                if ($fee->isEcmtIssuingFee()) {
                    $feeTypesAmounts[] = [
                        'issueFeeAmount' => $fee->getFeeTypeAmount(),
                        'issueFeeTotal' => $fee->getOutstandingAmount(),
                        'invoicedDate' => $fee->getInvoicedDateTime()
                    ];
                }
            }

            if (count($feeTypesAmounts) !== 1) {
                throw new \Exception('There should be exactly one issuing fee.');
            }

            $vars['issueFeeDeadlineDate'] = $this->calculateDueDate($feeTypesAmounts[0]['invoicedDate']);
            $vars['issueFeeAmount'] = str_replace('.00', '', $feeTypesAmounts[0]['issueFeeAmount']);
            $vars['issueFeeTotal'] = str_replace('.00', '', $feeTypesAmounts[0]['issueFeeTotal']);
        }

        return $vars;
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
    protected function calculateDueDate(\DateTime $date)
    {
        $date->add(new \DateInterval('P10D'));
        return $date->format('d F Y');
    }
}
