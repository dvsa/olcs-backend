<?php

namespace Dvsa\Olcs\CompaniesHouse\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use Interop\Container\ContainerInterface;
use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;
use Olcs\Logging\Log\Logger;

/**
 * Class ClientFactory
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $optionKeyName = 'companies_house';

    /**
     * @var array
     */
    protected $options;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \Dvsa\Olcs\CompaniesHouse\Service\Client
     * @throws \RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $client = new Client();

        $httpOptions = $this->getOptions($container, 'http');
        $httpClient = new HttpClient();
        $httpClient->setOptions($httpOptions);

        $authOptions = $this->getOptions($container, 'auth');
        if (isset($authOptions['username']) && isset($authOptions['password'])) {
            $httpClient->setAuth($authOptions['username'], $authOptions['password']);
        }

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);

        $client->setHttpClient($httpClient);

        $clientOptions = $this->getOptions($container, 'client');
        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException(sprintf('Missing required option %s.client.baseuri', $this->optionKeyName));
        }
        $client->setBaseUri($clientOptions['baseuri']);

        return $client;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ContainerInterface $container
     * @param string $key
     *
     * @throws \RuntimeException
     * @return \Zend\Stdlib\AbstractOptions
     */
    public function getOptions(ContainerInterface $container, $key)
    {
        if (is_null($this->options)) {
            $options = $container->get('Configuration');
            $this->options = isset($options[$this->optionKeyName]) ? $options[$this->optionKeyName] : array();
        }

        $options = isset($this->options[$key]) ? $this->options[$key] : null;

        if (null === $options) {
            throw new RuntimeException(
                sprintf('Options could not be found in "%s.%s".', $this->optionKeyName, $key)
            );
        }

        return $options;
    }
}
