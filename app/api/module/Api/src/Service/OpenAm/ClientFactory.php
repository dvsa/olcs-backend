<?php

/**
 * Client Factory
 */
namespace Dvsa\Olcs\Api\Service\OpenAm;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;

/**
 * Client Factory
 */
class ClientFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['openam']['username'], $config['openam']['password'])) {
            throw new RuntimeException('Cannot create service, config for open am api credentials is missing');
        }

        if (!isset($config['openam']['uri'])) {
            throw new RuntimeException('Cannot create service, config for open am api uri is missing');
        }

        $options = [];
        if (isset($config['openam']['http_client_options'])) {
            $options = $config['openam']['http_client_options'];
        }

        $httpClient = new HttpClient('', $options);

        $httpClientWrapper = new ClientAdapterLoggingWrapper();
        $httpClientWrapper->wrapAdapter($httpClient);

        $request = new Request();
        $headers = $request->getHeaders();
        $headers->addHeaderLine('Accept', 'application/json');
        $headers->addHeaderLine('Content-Type', 'application/json');
        $headers->addHeaderLine('X-OpenIDM-Username', $config['openam']['username']);
        $headers->addHeaderLine('X-OpenIDM-Password', $config['openam']['password']);
        $request->setUri($config['openam']['uri']);

        return new Client($httpClient, $request);
    }
}
