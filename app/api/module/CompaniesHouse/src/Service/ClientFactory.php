<?php

namespace Dvsa\Olcs\CompaniesHouse\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
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
        $client = new Client();

        $httpOptions = $this->getOptions($serviceLocator, 'http');
        $httpClient = new HttpClient();
        $httpClient->setOptions($httpOptions);

        $authOptions = $this->getOptions($serviceLocator, 'auth');
        if (isset($authOptions['username']) && isset($authOptions['password'])) {
            $httpClient->setAuth($authOptions['username'], $authOptions['password']);
        }

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);

        $client->setHttpClient($httpClient);

        $clientOptions = $this->getOptions($serviceLocator, 'client');
        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException(sprintf('Missing required option %s.client.baseuri', $this->optionKeyName));
        }
        $client->setBaseUri($clientOptions['baseuri']);

        return $client;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ServiceLocatorInterface $sl
     * @param string $key
     *
     * @throws \RuntimeException
     * @return \Zend\Stdlib\AbstractOptions
     */
    public function getOptions(ServiceLocatorInterface $sl, $key)
    {
        if (is_null($this->options)) {
            $options = $sl->get('Configuration');
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
