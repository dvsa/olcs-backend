<?php

namespace Dvsa\Olcs\Cli\Service\Queue\Consumer\Cpms;

use Dvsa\Olcs\Cli\Service\Queue\Consumer\AbstractConsumerServices;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ReportDownloadFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $sl = $container->getServiceLocator();

        return new ReportDownload(
            $sl->get(AbstractConsumerServices::class),
            $sl->get('QueryHandlerManager')
        );
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $services
     *
     * @return ReportDownload
     */
    public function createService(ServiceLocatorInterface $services)
    {
        return $this($services, ReportDownload::class);
    }
}
