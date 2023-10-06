<?php

namespace Dvsa\Olcs\DocumentShare\Service;

use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Laminas\Log\Logger;
use League\Flysystem\Filesystem;
use League\Flysystem\WebDAV\WebDAVAdapter;
use RuntimeException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Http\Client as HttpClient;
use Sabre\DAV\Client as SabreClient;
use LmcRbacMvc\Service\AuthorizationService;
use Interop\Container\ContainerInterface;

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
     * @param ContainerInterface $container
     *
     * @return HttpClient
     * @throws \RuntimeException
     */
    public function getHttpClient(ContainerInterface $container): HttpClient
    {
        $options = $this->getOptions($container, 'http');
        $httpClient = new HttpClient();
        $httpClient->setOptions($options);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);
        $wrapper->setShouldLogData(false);

        return $httpClient;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ContainerInterface $container Service Manager
     * @param string $key Key
     *
     * @return array
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

    /**
     * @param ContainerInterface $container
     *
     * @return string
     */
    private function getClientType(ContainerInterface $container): string
    {
        $authService = $container->get(AuthorizationService::class);
        /** @var Logger $logger */
        $logger = $container->get('Logger');
        /** @var User $currentUser */
        $currentUser = $authService->getIdentity()->getUser();

        $clientType = ($currentUser->getOsType() == User::USER_OS_TYPE_WINDOWS_10 || $currentUser->getOsType() == User::USER_OS_TYPE_NORTHERN_I) ? WebDavClient::class : DocManClient::class;
        if ($clientType === DocManClient::class) {
            //record if document share client is used for a particular user
            $logger->info(DocManClient::class . ' is used for user ' . $currentUser->getId() . ' with OS type ' . $currentUser->getOsType());
        }
        return $clientType;
    }

    /**
     * @param $clientOptions
     */
    private function validateWebDavConfig($clientOptions)
    {
        if (empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        if (empty($clientOptions['webdav_baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.webdav_baseuri');
        }

        if (empty($clientOptions['username'])) {
            throw new RuntimeException('Missing required option document_share.client.username');
        }

        if (empty($clientOptions['password'])) {
            throw new RuntimeException('Missing required option document_share.client.password');
        }
    }

    /**
     * @param $clientOptions
     *
     */
    private function validateDocManConfig($clientOptions)
    {
        if (!isset($clientOptions['workspace']) || empty($clientOptions['workspace'])) {
            throw new RuntimeException('Missing required option document_share.client.workspace');
        }

        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException('Missing required option document_share.client.baseuri');
        }
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $clientOptions = $this->getOptions($container, 'client');
        $clientOptions['httpClient'] = $this->getHttpClient($container);
        if ($this->getClientType($container) === WebDavClient::class) {
            $this->validateWebDavConfig($clientOptions);
            $sabreClient = new SabreClient(
                [
                    'baseUri' => $clientOptions['webdav_baseuri'],
                    'username' => $clientOptions['username'],
                    'password' => $clientOptions['password']
                ]
            );

            $adapter = new WebDAVAdapter($sabreClient, $clientOptions['workspace']);
            $fileSystem = new Filesystem($adapter);
            return new WebDavClient($fileSystem);
        } else {
            $this->validateDocManConfig($clientOptions);
            $client = new DocManClient(
                $this->getHttpClient($container),
                $clientOptions['baseuri'],
                $clientOptions['workspace']
            );
            if (isset($clientOptions['uuid'])) {
                $client->setUuid($clientOptions['uuid']);
            }
            return $client;
        }
    }
}
