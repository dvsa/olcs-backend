<?php

/**
 * Application - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OutstandingFees extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Fee', 'FeeType'];


        /*
         * Get fees pertaining to the application
         *
         * AC specify we should only get the *latest* application and interim
         * fees in the event there are multiple fees outstanding.
         */
    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo('Application')->fetchUsingId($query);

        $applicationFee = $this->getLatestOutstandingApplicationFeeForApplication($application);

        $outstandingFees = [];
        if ($applicationFee) {
            $outstandingFees[] = $this->result($applicationFee)->serialize();
            // [
            //     'licence',
            //     'feePayments' => [
            //         'payment'
            //     ]
            // ]
        }

        // @TODO get $interimFee
        return $this->result(
            $application,
            [],
            [
                'outstandingFees' => $outstandingFees,
            ]
        );
    }

    /**
     * @param ApplicationEntity $application
     */
    protected function getLatestOutstandingApplicationFeeForApplication($application)
    {
        $applicationType = $application->getApplicationType();
        $feeTypeFeeTypeId = ($applicationType == ApplicationEntity::APPLICATION_TYPE_VARIATION)
            ? FeeTypeEntity::FEE_TYPE_VAR
            : FeeTypeEntity::FEE_TYPE_APP;

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
