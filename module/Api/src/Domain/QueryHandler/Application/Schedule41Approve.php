<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;

/**
 * Schedule41Approve
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Schedule41Approve extends AbstractQueryHandler
{
    const ERROR_MUST_COMPETE_OC = 'S41_APP_APPROVE_OC';
    const ERROR_MUST_COMPETE_TM = 'S41_APP_APPROVE_TM';
    const ERROR_OUSTANDING_FEE = 'S41_APP_OUSTANDING_FEE';

    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;


    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            [],
            [
                'errors' => $this->validate($application),
            ]
        );
    }

    /**
     * Validate that the applications schedule41 can be approved
     *
     * @param ApplicationEntity $application
     * @return array of errors
     */
    private function validate(ApplicationEntity $application)
    {
        $errors = [];

        $applicationCompletion = $application->getApplicationCompletion();

        // The status of the operating centre section is NOT complete; AND/OR
        if ($applicationCompletion->getOperatingCentresStatus() !== ApplicationCompletion::STATUS_COMPLETE) {
            $errors[self::ERROR_MUST_COMPETE_OC] = 'Must complete Operating Centres';
        }

        // Application is new
        // The application licence type is standard national or international and
        // the transport manager section is NOT complete
        if ($application->isNew() &&
            ($application->isStandardNational() || $application->isStandardInternational()) &&
            $applicationCompletion->getTransportManagersStatus() !== ApplicationCompletion::STATUS_COMPLETE
        ) {
            $errors[self::ERROR_MUST_COMPETE_TM] = 'Must complete Transport Managers';
        }

        // There is an outstanding application fee;
        if (!empty($this->feesHelper->getOutstandingFeesForApplication($application->getId()))) {
            $errors[self::ERROR_OUSTANDING_FEE] = 'There is an outstanding application fee';
        }

        return $errors;
    }
}
