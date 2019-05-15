<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use RuntimeException;
use Sabre\DAV\Client as WebDAVClient;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientFactory
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

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
        $this->serviceLocator = $serviceLocator;

        $clientOptions = $this->getOptions('client');

        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }

        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        $webDAVClient = new WebDAVClient(
            [
                'baseUri' => $clientOptions['baseuri'],
                'username' => $clientOptions['username'],
                'password' => $clientOptions['password']
            ]
        );

        $adapter = new WebDAVAdapter($webDAVClient, $clientOptions['workspace']);
        $fileSystem = new Filesystem($adapter);

        $client = new Client($fileSystem);

        return $client;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param string $key Key
     *
     * @return \Zend\Stdlib\AbstractOptions
     * @throws \RuntimeException
     */
    private function getOptions($key)
    {
        if (is_null($this->options)) {
            $options = $this->serviceLocator->get('Configuration');
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
