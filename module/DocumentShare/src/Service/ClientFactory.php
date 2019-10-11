<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use RuntimeException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;

/**
 * Class ClientFactory
 */
class ClientFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return \Dvsa\Olcs\DocumentShare\Service\Client
     * @throws \RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $clientOptions = $this->getConfiguration($serviceLocator);

        $options = $this->getOptions($serviceLocator, 'http');
        $httpClient = new HttpClient();
        $httpClient->setOptions($options);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        $wrapper->setShouldLogData(false);

        $client = new Client(
            $httpClient,
            $clientOptions['baseuri'],
            $clientOptions['workspace']
        );

        if (isset($clientOptions['uuid'])) {
            $client->setUuid($clientOptions['uuid']);
        }

        return $client;

    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ServiceLocatorInterface $sl  Service Manager
     * @param string                  $key Key
     *
     * @return \Zend\Stdlib\AbstractOptions
     * @throws \RuntimeException
     */
    public function getOptions(ServiceLocatorInterface $sl, $key)
    {
        if (is_null($this->options)) {
            $options = $sl->get('Configuration');
            $this->options = isset($options['document_share']) ? $options['document_share'] : array();
        }

        $options = isset($this->options[$key]) ? $this->options[$key] : null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf(
                    'Options could not be found in "document_share.%s".',
                    $key
                )
            );
        }

        return $options;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @param                         $requestedName
     *
     * @return \Zend\Stdlib\AbstractOptions
     */
    private function getConfiguration(ServiceLocatorInterface $serviceLocator, $requestedName): \Zend\Stdlib\AbstractOptions
    {
        $clientOptions = $this->getOptions($serviceLocator, 'client');
        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }

        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        if($requestedName !== Client::class)
        {

            if (!isset($clientOptions['username']) || empty($clientOptions['username'])) {
                throw new RuntimeException('Missing required option document_share.client.username for webdav client');
            }

            if (!isset($clientOptions['password']) || empty($clientOptions['password'])) {
                throw new RuntimeException('Missing required option document_share.client.password for webdav client');
            }
        }

        return $clientOptions;
}

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return Client::class;
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         $name
     * @param                         $requestedName
     *
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $clientOptions = $this->getConfiguration($serviceLocator, $requestedName);
        return $requestedName($clientOptions);
}}
