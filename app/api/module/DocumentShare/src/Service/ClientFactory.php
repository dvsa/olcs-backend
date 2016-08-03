<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\Api\Filesystem\Filesystem;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
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
        $options = $this->getOptions($serviceLocator, 'http');

        $httpClient = new HttpClient();
        $httpClient->setOptions($options);

        $clientOptions = $this->getOptions($serviceLocator, 'client');

        $request = new Request();

        if (isset($clientOptions['uuid'])) {
            $request->getHeaders()->addHeaderLine('uuid', $clientOptions['uuid']);
        }

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        $wrapper->setShouldLogData(false);

        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }

        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        return new Client(
            $httpClient,
            $request,
            new Filesystem(),
            $clientOptions['baseuri'],
            $clientOptions['workspace']
        );
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
}
