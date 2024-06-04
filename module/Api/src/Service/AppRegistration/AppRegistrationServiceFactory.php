<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\AppRegistration;

use Dvsa\Olcs\Api\Service\AppRegistration\Adapter\AppRegistrationSecret;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleRetry\GuzzleRetryMiddleware;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Olcs\Logging\Log\LaminasLogPsr3Adapter;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;

class AppRegistrationServiceFactory implements FactoryInterface
{
    /**
     * @var mixed
     */
    private $options;

    /**
     * invoke method
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param                                         $requestedName
     * @param array|null $options
     * @return AppRegistrationInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AppRegistrationInterface
    {
        $config = $container->get('config');
        $this->options = $config['app-registrations'];
        $logger = new LaminasLogPsr3Adapter($container->get('Logger'));
        $stack = HandlerStack::create();
        $stack->push(GuzzleRetryMiddleware::factory());
        $httpClient = new HttpClient(
            [
            'handler' => $stack,
            'proxy' => $this->getOptions('proxy'),
            'headers' => [
                'user-agent' => 'olcs-app-registration'
            ],
            'max_retry_attempts' => $this->getOptions('max_retry_attempts', 3),
            'retry_on_status' => [
                500,
                503
            ]
            ]
        );

        // get the secret
        $secret = $container->get(AppRegistrationSecret::class);
        return new $requestedName($httpClient, $this->options, $secret, $logger);
    }

    protected function getOptions(string $key, $default = null)
    {
        $options = $this->options[$key] ?? $default;

        if (null === $options) {
            throw new RuntimeException("Option could not be found: {$key}");
        }

        return $options;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @deprecated remove following laminas v3 upgrade
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, $requestedName);
    }
}
