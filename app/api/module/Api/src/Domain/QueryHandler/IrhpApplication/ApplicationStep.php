<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Dvsa\Olcs\Transfer\Query\Qa\ApplicationStep as ApplicationStepQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application step
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStep extends AbstractQueryHandler
{
    /** @var ApplicationStepObjectsProvider */
    private $applicationStepObjectsProvider;

    /** @var SelfservePageGenerator */
    private $selfservePageGenerator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->applicationStepObjectsProvider = $mainServiceLocator->get('QaApplicationStepObjectsProvider');
        $this->selfservePageGenerator = $mainServiceLocator->get('QaSelfservePageGenerator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|ApplicationStepQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $objects = $this->applicationStepObjectsProvider->getObjects(
            $query->getId(),
            $query->getSlug()
        );

        $selfservePage = $this->selfservePageGenerator->generate(
            $objects['applicationStep'],
            $objects['irhpApplication']
        );

        return $selfservePage->getRepresentation();
    }
}
