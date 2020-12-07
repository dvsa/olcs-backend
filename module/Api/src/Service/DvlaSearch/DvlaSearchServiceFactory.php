<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\DvlaSearch;

use Dvsa\Olcs\DvlaSearch\Service\ClientFactory;
use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class DvlaSearchServiceFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $logger = new LaminasLogPsr3Adapter($serviceLocator->get('logger'));
        $clientFactory = new ClientFactory();

        return $clientFactory->createService($config['dvla_search'], $logger);
    }
}
