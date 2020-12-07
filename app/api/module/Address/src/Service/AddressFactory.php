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

/**
 * Address Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AddressFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['address']['client']['baseuri'])) {
            throw new \RuntimeException('Address service baseuri not set');
        }

        $client = new Client($config['address']['client']['baseuri']);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($client);

        return new Address($client);
    }
}
