<?php

/**
 * Client Factory
 */

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request;
use Laminas\ServiceManager\Exception\RuntimeException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;

/**
 * Client Factory
 */
class ClientFactory implements FactoryInterface
{
    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return Client
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): Client
    {
        $config = $container->get('Config');
        if (!isset($config['openam']['username'], $config['openam']['password'])) {
            throw new \RuntimeException('Cannot create service, config for open am api credentials is missing');
        }
        if (!isset($config['openam']['uri'])) {
            throw new \RuntimeException('Cannot create service, config for open am api uri is missing');
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
