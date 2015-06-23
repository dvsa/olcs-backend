<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;

/**
 * Application Outstanding Fees trait, can be shared by Commands and Queries
 *
 * @note requires Application, Fee and FeeType repos
 */
trait ApplicationOutstandingFeesTrait
{
    /**
     * Get fees pertaining to an application
     *
     * AC specify we should only get the *latest* application and interim
     * fees in the event there are multiple fees outstanding.
     *
     * @param int $applicationId
     * @return array
     */
    protected function getOutstandingFeesForApplication($applicationId)
    {
        $outstandingFees = [];

        $application = $this->getRepo('Application')->fetchById($applicationId);

        // get application fee
        $applicationFee = $this->getLatestOutstandingApplicationFeeForApplication($application);
        if (!empty($applicationFee)) {
            $outstandingFees[] = $applicationFee;
        }

        // get interim fee if applicable
        if ($application->isGoods()) {
            $interimFee = $this->getLatestOutstandingInterimFeeForApplication($application);
            if (!empty($interimFee)) {
                $outstandingFees[] = $interimFee;
            }
        }

        return $outstandingFees;
    }

    /**
     * @param ApplicationEntity $application
     * @return FeeEntity
     */
    protected function getLatestOutstandingApplicationFeeForApplication(ApplicationEntity $application)
    {
        if ($application->isVariation()) {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_VAR;
        } else {
            $feeTypeFeeTypeId = FeeTypeEntity::FEE_TYPE_APP;
        }

        return $this->getLatestOutstandingFeeForApplicationByType($application, $feeTypeFeeTypeId);
    }

    /**
     * @param ApplicationEntity $application
     * @return FeeEntity
     */
    protected function getLatestOutstandingInterimFeeForApplication(ApplicationEntity $application)
    {
        return $this->getLatestOutstandingFeeForApplicationByType($application, FeeTypeEntity::FEE_TYPE_GRANTINT);
    }

    /**
     * @param ApplicationEntity $application
     * @param string $feeTypeFeeTypeId
     * @return FeeEntity
     */
    protected function getLatestOutstandingFeeForApplicationByType($application, $feeTypeFeeTypeId)
    {
        $applicationDate = new \DateTime($application->getApplicationDate());

        $feeType = $this->getRepo('FeeType')->fetchLatest(
            $this->getRepo()->getRefdataReference($feeTypeFeeTypeId),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $applicationDate,
            $application->getLicence()->getTrafficArea()
        );

        return $this->getRepo('Fee')->fetchLatestFeeByTypeStatusesAndApplicationId(
            $feeType->getId(),
            [FeeEntity::STATUS_OUTSTANDING, FeeEntity::STATUS_WAIVE_RECOMMENDED],
            $application->getId()
        );
    }
}
