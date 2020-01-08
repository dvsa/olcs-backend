<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Traits;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * ECMT Annual Permit email trait
 */
trait EcmtAnnualPermitEmailTrait
{
    /**
     * Get template variables
     *
     * @param IrhpApplication $recordObject
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
            'ecmtGuidanceUrl' => 'https://www.gov.uk/guidance/ecmt-international-road-haulage-permits',
            'applicationRef' => $recordObject->getApplicationRef(),
            'applicationFee' => $this->formatCurrency(
                $this->getRepo('FeeType')->getLatestByProductReference(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF)->getAmount()
            ),
        ];

        if ($recordObject->isAwaitingFee()) {
            /** @var IrhpPermitApplication $irhpPermitApplication */
            $irhpPermitApplication = $recordObject->getIrhpPermitApplications()->first();

            $vars['awaitingFeeUrl'] = 'http://selfserve/permits/' . (int)$recordObject->getId() . '/ecmt-awaiting-fee/';
            $vars['permitsRequired'] = $recordObject->calculateTotalPermitsRequired();
            $vars['permitsGranted'] = $irhpPermitApplication->countPermitsAwarded();
            // TODO - OLCS-21979
            $vars['paymentDeadlineNumDays'] = '10';
            $vars['validityYear'] = $irhpPermitApplication->getIrhpPermitWindow()
                ->getIrhpPermitStock()
                ->getValidityYear();

            $criteria = Criteria::create();
            $criteria->where(
                $criteria->expr()->eq(
                    'feeStatus',
                    $this->getRepo()->getRefdataReference(FeeEntity::STATUS_OUTSTANDING)
                )
            );

            $fees = $recordObject->getFees()->matching($criteria);
            $feeTypesAmounts = [];

            /** @var FeeEntity $fee */
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
            $vars['issueFeeAmount'] = $this->formatCurrency($feeTypesAmounts[0]['issueFeeAmount']);
            $vars['issueFeeTotal'] = $this->formatCurrency($feeTypesAmounts[0]['issueFeeTotal']);
        }

        return $vars;
    }

    /**
     * {@inheritdoc}
     */
    protected function getTranslateToWelsh($recordObject)
    {
        return $recordObject->getLicence()->getTranslateToWelsh();
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
