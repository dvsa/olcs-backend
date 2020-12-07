<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Service\Lva\Application\GrantValidationService;

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Grant extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var GrantValidationService
     */
    private $grantValidationService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->grantValidationService = $mainServiceLocator->get('ApplicationGrantValidationService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $messages = $this->grantValidationService->validate($application);
        $canGrant = empty($messages);

        return $this->result(
            $application,
            [],
            [
                'canGrant' => $canGrant,
                'reasons' => $messages,
                'canHaveInspectionRequest' => $canGrant && $this->canHaveInspectionRequest($application)
            ]
        );
    }

    protected function canHaveInspectionRequest(ApplicationEntity $application)
    {
        return !$application->isVariation() && !$application->isSpecialRestricted();
    }
}
