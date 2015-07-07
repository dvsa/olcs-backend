<?php

/**
 * Fees Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Service;

use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fees Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FeesHelperService implements FactoryInterface
{
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Application
     */
    protected $applicationRepo;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Fee
     */
    protected $feeRepo;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\FeeType
     */
    protected $feeTypeRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $repoManager = $serviceLocator->get('RepositoryServiceManager');

        // inject required repos
        $this->applicationRepo = $repoManager->get('Application');
        $this->feeRepo = $repoManager->get('Fee');
        $this->feeTypeRepo = $repoManager->get('FeeType');

        return $this;
    }

    /**
     * Get fees pertaining to an application
     *
     * AC specify we should only get the *latest* application and interim
     * fees in the event there are multiple fees outstanding.
     *
     * @param int $applicationId
     * @return array
     */
    public function getOutstandingFeesForApplication($applicationId)
    {
        $outstandingFees = [];

        $application = $this->applicationRepo->fetchById($applicationId);

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
        if (is_null($application->getLicenceType())) {
            return;
        }

        $applicationDate = new \DateTime($application->getApplicationDate());

        $feeType = $this->feeTypeRepo->fetchLatest(
            $this->feeTypeRepo->getRefdataReference($feeTypeFeeTypeId),
            $application->getGoodsOrPsv(),
            $application->getLicenceType(),
            $applicationDate,
            $application->getLicence()->getTrafficArea()
        );

        return $this->feeRepo->fetchLatestFeeByTypeStatusesAndApplicationId(
            $feeType->getId(),
            [FeeEntity::STATUS_OUTSTANDING, FeeEntity::STATUS_WAIVE_RECOMMENDED],
            $application->getId()
        );
    }
}
