<?php

namespace Dvsa\Olcs\Email\Service;

use Dvsa\Olcs\Utils\Client\ClientAdapterLoggingWrapper;
use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Http\Client as HttpClient;

/**
 * Class ClientFactory
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class ClientFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $optionKeyName = 'email';

    /**
     * @var array
     */
    protected $options;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return \Dvsa\Olcs\Email\Service\Client
     * @throws \RuntimeException
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $client = new Client();

        $httpOptions = $this->getOptions($serviceLocator, 'http');
        $httpClient = new HttpClient();
        $httpClient->setOptions($httpOptions);

        $wrapper = new ClientAdapterLoggingWrapper();
        $wrapper->wrapAdapter($httpClient);

        $client->setHttpClient($httpClient);

        $clientOptions = $this->getOptions($serviceLocator, 'client');
        if (!isset($clientOptions['baseuri']) || empty($clientOptions['baseuri'])) {
            throw new RuntimeException(sprintf('Missing required option %s.client.baseuri', $this->optionKeyName));
        }
        $client->setBaseUri($clientOptions['baseuri']);

        if (isset($clientOptions['from_email']) && isset($clientOptions['from_name'])) {
            $client->setDefaultFrom($clientOptions['from_email'], $clientOptions['from_name']);
        }
        if ($serviceLocator->has('translator')) {
            $client->setTranslator($serviceLocator->get('translator'));
        }
        if (isset($clientOptions['selfserve_uri'])) {
            $client->setSelfServeUri($clientOptions['selfserve_uri']);
        }
        if (isset($clientOptions['send_all_mail_to'])) {
            $client->setSendAllMailTo($clientOptions['send_all_mail_to']);
        }

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
