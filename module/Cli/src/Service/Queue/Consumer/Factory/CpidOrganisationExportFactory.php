<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Cpid Organisation Export Factory
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CpidOrganisationExportFactory implements FactoryInterface
{
    /**
     * Factory
     *
     * @param \Dvsa\Olcs\Cli\Service\Queue\MessageConsumerManager $serviceLocator Manager
     *
     * @return CpidOrganisationExport
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $repo = $container->get('RepositoryServiceManager')->get('Organisation');
        /** @var \Dvsa\Olcs\Api\Domain\CommandHandlerManager $commandHandler */
        $commandHandler = $container->get('CommandHandlerManager');

        return new CpidOrganisationExport($repo, $commandHandler);
    }
}
