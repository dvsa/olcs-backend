<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\DvlaSearch;

use Dvsa\Olcs\DvlaSearch\Service\ClientFactory;
use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class DvlaSearchServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, ClientFactory::class);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        $logger = new LaminasLogPsr3Adapter($container->get('logger'));
        $clientFactory = new ClientFactory();
        return $clientFactory->createService($config['dvla_search'], $logger);
    }
}
