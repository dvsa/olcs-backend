<?php

/**
 * Client Factory
 */
namespace Dvsa\Olcs\Api\Service\OpenAm;

use Interop\Container\ContainerInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config');

        if (!isset($config['openam']['username'], $config['openam']['password'])) {
            throw new InvalidServiceException('Cannot create service, config for open am api credentials is missing');
        }

        if (!isset($config['openam']['uri'])) {
            throw new InvalidServiceException('Cannot create service, config for open am api uri is missing');
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
