<?php

namespace Dvsa\Olcs\Api\Service\Template;

use Twig\Environment;
use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class TwigEnvironmentFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Environment
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Environment(
            $serviceLocator->get('TemplateDatabaseTwigLoader'),
            [
                'strict_variables' => true,
                'cache' => sys_get_temp_dir(),
                'auto_reload' => true,
            ]
        );
    }
}
