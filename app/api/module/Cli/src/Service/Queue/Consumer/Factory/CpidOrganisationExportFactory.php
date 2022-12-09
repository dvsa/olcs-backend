<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Factory;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

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
    public function createService(ServiceLocatorInterface $serviceLocator): CpidOrganisationExport
    {
        return $this->__invoke($serviceLocator, CpidOrganisationExport::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CpidOrganisationExport
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CpidOrganisationExport
    {
        /** @var \Laminas\ServiceManager\ServiceManager $sl */
        $sl = $container->getServiceLocator();
        return new CpidOrganisationExport(
            $sl->get(AbstractConsumerServices::class),
            $sl->get('RepositoryServiceManager')->get('Organisation')
        );
    }
}
