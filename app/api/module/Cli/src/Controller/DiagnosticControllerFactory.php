<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Dvsa\Olcs\CompaniesHouse\Service\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Diagnostic Controller Factory
 */
class DiagnosticControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return IrhpCandidatePermitsCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): DiagnosticController
    {
        return $this->__invoke($serviceLocator, DiagnosticController::class);
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
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): DiagnosticController
    {
        $sm = $container->getServiceLocator();

        $config = $sm->get('config');
        $cpmsHelperService = $sm->get('CpmsHelperService');
        $elasticSearch = $sm->get('ElasticSearch\Search');
        $addressService = $sm->get('AddressService');
        $companiesHouseService = $sm->get(Client::class);
        $imapService = $sm->get('ImapService');
        $nrService = $sm->get(InrClientInterface::class);
        $queryHandlerManager = $sm->get('QueryHandlerManager');
        $commandHandlerManager = $sm->get('CommandHandlerManager');

        return new DiagnosticController(
            $config,
            $cpmsHelperService,
            $elasticSearch,
            $addressService,
            $companiesHouseService,
            $imapService,
            $nrService,
            $queryHandlerManager,
            $commandHandlerManager
        );
    }
}
