<?php

/**
 * Address Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Address\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

/**
 * Address Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, Address::class);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');
        if (!isset($config['address']['client']['baseuri'])) {
            throw new \RuntimeException('Address service baseuri not set');
        }
        $client = new Client($config['address']['client']['baseuri']);
        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($client);
        return new Address($client);
    }
}
