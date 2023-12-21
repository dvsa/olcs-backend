<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Dvsa\Olcs\Api\Service\Permits\CandidatePermits\IrhpCandidatePermitsCreator;
use Dvsa\Olcs\CompaniesHouse\Service\Client;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Diagnostic Controller Factory
 */
class DiagnosticControllerFactory implements FactoryInterface
{
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
        $config = $container->get('config');
        $cpmsHelperService = $container->get('CpmsHelperService');
        $elasticSearch = $container->get('Elasticsearch\Search');
        $addressService = $container->get('AddressService');
        $companiesHouseService = $container->get(Client::class);
        $imapService = $container->get('ImapService');
        $nrService = $container->get(InrClientInterface::class);
        $queryHandlerManager = $container->get('QueryHandlerManager');
        $commandHandlerManager = $container->get('CommandHandlerManager');

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
