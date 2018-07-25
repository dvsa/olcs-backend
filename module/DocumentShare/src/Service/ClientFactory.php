<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request;

/**
 * Class ClientFactory
 */
class ClientFactory implements FactoryInterface
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
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $clientOptions = $this->getOptions($container, 'client');
        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }

        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        $options = $this->getOptions($container, 'http');
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
     * @param ContainerInterface $container ContainerInterface
     * @param string             $key       Key
     *
     * @return \Zend\Stdlib\AbstractOptions
     * @throws \RuntimeException
     */
    public function getOptions(ContainerInterface $container, $key)
    {
        if (is_null($this->options)) {
            $options = $container->get('Configuration');
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
}
