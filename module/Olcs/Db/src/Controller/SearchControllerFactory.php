<?php

namespace Olcs\Db\Controller;

use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Search Controller Factory
 */
class SearchControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SearchController
    {
        return $this->__invoke($serviceLocator, SearchController::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return IrhpCandidatePermitsCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SearchController
    {
        $sm = $container->getServiceLocator();
        $elasticSearchService = $sm->get('ElasticSearch\Search');

        return new SearchController(
            $elasticSearchService
        );
    }
}
