<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Twig\Environment;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class TwigEnvironmentFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Environment
     */
    public function createService(ServiceLocatorInterface $serviceLocator): Environment
    {
        return $this->__invoke($serviceLocator, Environment::class);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Environment(
            $container->get('TemplateDatabaseTwigLoader'),
            [
                'strict_variables' => true,
                'cache' => sys_get_temp_dir(),
                'auto_reload' => true,
            ]
        );
    }
}
