<?php

/**
 * Address Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Address\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Address Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
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
