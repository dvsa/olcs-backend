<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\SecretsManager;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocalSecretsManagerFactory implements FactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LocalSecretsManager
    {
        $config = $container->get('config');
        return new LocalSecretsManager($config['localSecretsManager']);
    }
}
