<?php

namespace Dvsa\Olcs\Api\Service\OpenAm;

use Zend\Http\Client as HttpClient;
use Zend\Http\Header\Accept;
use Zend\Http\Request;
use Zend\ServiceManager\Exception\RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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
        $httpClient = new HttpClient($config['openam']['uri'], $options);

        $request = new Request();
        $headers = $request->getHeaders();
        $headers->addHeader(new Accept('application/json'));
        $headers->addHeaderLine('X-OpenIDM-Username', $config['openam']['username']);
        $headers->addHeaderLine('X-OpenIDM-Password', $config['openam']['password']);

        return new Client($httpClient, $request);
    }
}
