<?php

namespace Dvsa\Olcs\DocumentShare\Service;

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
     * @param ServiceLocatorInterface $serviceLocator
     * @throws \RuntimeException
     * @return \Dvsa\Olcs\DocumentShare\Service\Client
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

        $client = new Client();
        $client->setHttpClient($httpClient);
        $client->setRequestTemplate($request);

        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }

        $client->setBaseUri($clientOptions['baseuri']);

        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        $client->setWorkspace($clientOptions['workspace']);

        return $client;

    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ServiceLocatorInterface $sl
     * @param string $key
     * @throws \RuntimeException
     * @return \Zend\Stdlib\AbstractOptions
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
